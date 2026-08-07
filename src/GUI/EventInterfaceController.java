/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.URL;
import java.nio.file.Files;
import java.nio.file.Paths;
import static java.nio.file.StandardCopyOption.REPLACE_EXISTING;
import java.sql.Connection;
import java.sql.Date;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Time;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.time.LocalTime;
import java.util.List;
import java.util.ResourceBundle;
import java.util.logging.Level;
import java.util.logging.Logger;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.collections.transformation.FilteredList;
import javafx.collections.transformation.SortedList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.Node;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Label;
import javafx.scene.control.Pagination;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.input.MouseEvent;
import javafx.stage.FileChooser;
import net.glxn.qrgen.QRCode;
import entity.Category;
import entity.Evenement;
import Services_event.ServiceCategorie;
import Services_event.ServiceEvent;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import tools.MaConnection;
import tray.notification.NotificationType;
import tray.notification.TrayNotification;

/**
 * FXML Controller class
 *
 * @author msi
 */
public class EventInterfaceController implements Initializable {

    private TextField nbr;
    @FXML
    private TableColumn<Evenement, Integer> idT;
    @FXML
    private TableView<Evenement> tableEvent;
    @FXML
    private TableColumn<Evenement, String> titreT;
    @FXML
    private TableColumn<Evenement, String> descriptionT;
    @FXML
    private TableColumn<Evenement, ImageView> imageT;
    @FXML
    private TableColumn<Evenement, Date> dateT;    
    @FXML
    private TableColumn<Evenement, String> categoryT;
    @FXML
    private Label idlabel;
        
    private Statement ste;
    private Connection con;
    ServiceCategorie sc= new ServiceCategorie();
    ServiceEvent se= new ServiceEvent();
    
    private final ObservableList<Evenement> data = FXCollections.observableArrayList();
    private final ObservableList<Evenement> dataa = FXCollections.observableArrayList();
    
    ObservableList<String> listcategory = FXCollections.observableArrayList();

    @FXML
    private TextField titretxt;
    @FXML
    private TextField desctxt;
    @FXML
    private TextField affiche;
    @FXML
    private Button uploadbutton;
    @FXML
    private ComboBox<String> ComboCategorie;
    @FXML
    private DatePicker dateevent;
    @FXML
    private Label labelPATH;
    @FXML
    private TextField recherche;
    @FXML
    private Pagination pagination;
    private final static int rowPerPage= 4;
    private final  TableView<Evenement> table = createTable();

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
                try {
            // TODO
            fillcombo();
        } catch (SQLException ex) {
            Logger.getLogger(EventInterfaceController.class.getName()).log(Level.SEVERE, null, ex);
        }
        Aff();
    }    
    
    private Node createPage(int pageIndex){
        int fromIndex = pageIndex * rowPerPage;
        int toIndex = Math.min(fromIndex + rowPerPage,dataa.size());
        table.setItems(FXCollections.observableArrayList(dataa.subList(fromIndex, toIndex)));
        return table;
    }
    
    private TableView<Evenement> createTable(){
                                        try {
            con = MaConnection.getInstance().getCnx();    
            ste = con.createStatement();
            dataa.clear();

            ResultSet rs = ste.executeQuery("select * from evenement");
            while(rs.next()){
                Evenement f = new Evenement(rs.getInt("id"), rs.getInt("category_id"),rs.getString("titre"), rs.getString("description"),rs.getString("image"),rs.getDate("date"));
                                
                File file = new File("C:\\xampp\\htdocs\\imagesEvenet\\"+rs.getString("image")); 
                Image image = new Image(file.toURI().toString());
                
                ImageView imageView =new ImageView(image);
                imageView.setImage(image);
                imageView.setFitHeight(100);
                imageView.setFitWidth(100);

                f.setImg(imageView);                

                Category d = sc.getById(f.getId_categorie());
                f.setCattegory(d);
                f.setCategorieNom(d.getNom());
                dataa.add(f);
            }
        } catch (Exception e) {
                //Logger.getLogger(tab)
        }
                      
        TableView<Evenement> table = new TableView<>() ;
        TableColumn<Evenement,Integer> idT = new TableColumn<>("id");
        TableColumn<Evenement,String> titreT = new TableColumn<>("titre");
        TableColumn<Evenement,String> descriptionT = new TableColumn<>("description");
        TableColumn<Evenement,ImageView> imageTT = new TableColumn<>("image");
        TableColumn<Evenement,String> categoryT = new TableColumn<>("categorieNom");
        TableColumn<Evenement,Date> dateT = new TableColumn<>("dateev");
                   
            idT.setCellValueFactory(new PropertyValueFactory<>("id"));
            titreT.setCellValueFactory(new PropertyValueFactory<>("titre"));
            descriptionT.setCellValueFactory(new PropertyValueFactory<>("description"));
            imageTT.setCellValueFactory(new PropertyValueFactory<>("img"));
            categoryT.setCellValueFactory(new PropertyValueFactory<>("categorieNom"));
            dateT.setCellValueFactory(new PropertyValueFactory<>("dateev"));
            table.setItems(dataa);
               
        table.getColumns().addAll(idT,titreT,descriptionT,imageTT,categoryT,dateT);
        return table;
    }
        public void fillcombo() throws SQLException{     
        List<Category> list = sc.readAll();
        for (Category aux : list)
        {
          listcategory.add(aux.getNom());
        }
        ComboCategorie.setItems(listcategory);
    }
            public void Aff(){
            pagination.setPageFactory(this::createPage);

                                try {
            con = MaConnection.getInstance().getCnx();    
            ste = con.createStatement();
            data.clear();

            ResultSet rs = ste.executeQuery("select * from evenement");
            while(rs.next()){
                             
                Evenement f = new Evenement(rs.getInt("id"), rs.getInt("category_id"),rs.getString("titre"), rs.getString("description"),rs.getString("image"),rs.getDate("date"));

                File file = new File("C:\\xampp\\htdocs\\imagesEvenet\\"+f.getImage()); 
                System.out.println(file.toURI().toString());
                Image image = new Image(file.toURI().toString());
                
                ImageView imageView =new ImageView(image);
                imageView.setImage(image);
                imageView.setFitHeight(100);
                imageView.setFitWidth(100);

                f.setImg(imageView);                

                Category d = sc.getById(f.getId_categorie());
                f.setCattegory(d);
                f.setCategorieNom(d.getNom());
                data.add(f);
            }
        } catch (Exception e) {
                //Logger.getLogger(tab)
        }
                                
            idT.setCellValueFactory(new PropertyValueFactory<>("id"));
            titreT.setCellValueFactory(new PropertyValueFactory<>("titre"));
            descriptionT.setCellValueFactory(new PropertyValueFactory<>("description"));
            imageT.setCellValueFactory(new PropertyValueFactory<>("img"));
            categoryT.setCellValueFactory(new PropertyValueFactory<>("categorieNom"));
            dateT.setCellValueFactory(new PropertyValueFactory<>("dateev"));
            tableEvent.setItems(data);
            RechercheAV();


    }
    public void RechercheAV(){
                // Wrap the ObservableList in a FilteredList (initially display all data).
        FilteredList<Evenement> filteredData = new FilteredList<>(data, b -> true);
		
		// 2. Set the filter Predicate whenever the filter changes.
		recherche.textProperty().addListener((observable, oldValue, newValue) -> {
			filteredData.setPredicate(tmp -> {
				// If filter text is empty, display all persons.
								
				if (newValue == null || newValue.isEmpty()) {
					return true;
				}
				
				// Compare first name and last name of every person with filter text.
				String lowerCaseFilter = newValue.toLowerCase();
				
				if (tmp.getDescription().toLowerCase().indexOf(lowerCaseFilter) != -1 ) {
					return true; // Filter matches first name.
				} else if (tmp.getTitre().toLowerCase().indexOf(lowerCaseFilter)!=-1)
				     return true;
				     else  
				    	 return false; // Does not match.
			});
		});
		
		// 3. Wrap the FilteredList in a SortedList. 
		SortedList<Evenement> sortedData = new SortedList<>(filteredData);
		
		// 4. Bind the SortedList comparator to the TableView comparator.
		// 	  Otherwise, sorting the TableView would have no effect.
		sortedData.comparatorProperty().bind(tableEvent.comparatorProperty());
		
		// 5. Add sorted (and filtered) data to the table.
		tableEvent.setItems(sortedData);
    }  
    @FXML
    private void Ajouter(ActionEvent event) throws ParseException, SQLException, IOException {
               
        Date dateev = java.sql.Date.valueOf(dateevent.getValue());
        java.util.Date today = new java.util.Date();  

        if (dateev.after(today)) {
            
                    if(Validchamp(titretxt) && Validchamp(desctxt))
        {
                    
        Category cat= sc.getByName(ComboCategorie.getValue());
        Evenement r = new Evenement(cat.getId(),titretxt.getText(), desctxt.getText() , affiche.getText(),dateev);
                     
        File f = new File(labelPATH.getText());
 
         Files.copy(Paths.get(labelPATH.getText()),Paths.get("C:\\xampp\\htdocs\\imagesEvenet\\"+f.getName()),REPLACE_EXISTING);

         se.ajouter(r);
            QRcode(r);

         Aff();
             desctxt.setText("");
             titretxt.setText("");
             idlabel.setText("");

        TrayNotification tray = new TrayNotification();
        tray.setTitle("Evenement : "+r.getTitre());
        tray.setMessage("Ajouté avec succès");
        tray.setNotificationType(NotificationType.SUCCESS);
        tray.showAndWait();

        }
        else
        {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("ERROR");
        alert.setHeaderText(null);
        alert.setContentText("Verifiez les champs!");

        alert.showAndWait();
        }
        
        } else {
            // invalid (Date <= today)
                    
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("ERROR");
        alert.setHeaderText(null);
        alert.setContentText("Date Invalide!");

        alert.showAndWait();

            
        }

         
    }

    @FXML
    private void Modifier(ActionEvent event) throws SQLException, ParseException, IOException {
        if(Validchamp(titretxt) && Validchamp(desctxt))
        {
        Category cat= sc.getByName(ComboCategorie.getValue());

        Date dateev = java.sql.Date.valueOf(dateevent.getValue());

        Evenement r = new Evenement(Integer.valueOf(idlabel.getText()),cat.getId(),titretxt.getText(), desctxt.getText() , affiche.getText(),dateev);
        if(!labelPATH.getText().equals(""))
        {
        File f = new File(labelPATH.getText());
         Files.copy(Paths.get(labelPATH.getText()),Paths.get("C:\\xampp\\htdocs\\imagesEvenet\\"+f.getName()),REPLACE_EXISTING);
        }
        QRcode(r);
        se.update(r);
        Aff();

        TrayNotification tray = new TrayNotification();
        tray.setTitle("Evenement : "+r.getTitre());
        tray.setMessage("Modifié avec succès");
        tray.setNotificationType(NotificationType.WARNING);
        tray.showAndWait();

        }
        else
        {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("ERROR");
        alert.setHeaderText(null);
        alert.setContentText("Verifiez les champs!");

        alert.showAndWait();
        }        
        
    }

    @FXML
    private void LoadData(MouseEvent event) {
                    
            ObservableList<Evenement> all,Single;
            Single=tableEvent.getSelectionModel().getSelectedItems();
            Evenement A = Single.get(0);
            
            idlabel.setText(String.valueOf(A.getId()));
            titretxt.setText(A.getTitre());
            desctxt.setText(A.getDescription());
            LocalDate localDate = LocalDate.parse(A.getDateev().toString());
            dateevent.setValue(localDate);
                 
            ComboCategorie.setValue(A.getCategorieNom());
            labelPATH.setText("");
            affiche.setText(A.getImage());

    }

    @FXML
    private void Supprimer(ActionEvent event) throws SQLException {
                     
             tableEvent.setItems(data);

             ObservableList<Evenement> allevents,Singleevent ;
             allevents=tableEvent.getItems();
             Singleevent=tableEvent.getSelectionModel().getSelectedItems();
             Evenement A = Singleevent.get(0);
             se.delete(A);
             Singleevent.forEach(allevents::remove);
             Aff();
                   
                TrayNotification tray = new TrayNotification();
                tray.setTitle("Evenement "+A.getTitre()+" Supprimé");
                tray.setMessage("Supprimé avec succès");
                tray.setNotificationType(NotificationType.ERROR);
                tray.showAndWait();

    }
        
    private boolean Validchamp(TextField T){
        //return !T.getText().isEmpty() && T.getLength() > 2;
        return !T.getText().isEmpty();
    }

    @FXML
    private void Uploadfile(ActionEvent event) {
             
        FileChooser fc = new FileChooser();
        String path = fc.showOpenDialog(uploadbutton.getScene().getWindow()).getPath();
                
        File f = new File(path);

        labelPATH.setText(f.getPath());
        affiche.setText(f.getName());

    }

        public static String projectPath = System.getProperty("user.dir").replace("\\", "/");
        private void QRcode(Evenement r) throws FileNotFoundException, IOException {
        String contenue = "Titre : " + r.getTitre()+ "\n" + "Date: " + r.getDateev().toString(); 
        ByteArrayOutputStream out = QRCode.from(contenue).to(net.glxn.qrgen.image.ImageType.JPG).stream();
        File f = new File(projectPath  + r.getTitre().toString()+ ".jpg");
        FileOutputStream fos = new FileOutputStream(f); //creation du fichier de sortie
        fos.write(out.toByteArray()); //ecrire le fichier du sortie converter
        fos.flush(); // creation final

    }
    @FXML
private void retour(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
   
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/AfficheAdmiin.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}
}
