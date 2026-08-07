/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import static com.itextpdf.text.pdf.XfaXpathConstructor.XdpPackage.Pdf;
import entity.Pdf;
import entity.User;
import gestionreclamationsante.FXMain;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.lang.reflect.InvocationTargetException;
import java.net.URL;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Date;
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
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.print.PageOrientation;
import javafx.print.Paper;
import javafx.print.Printer;
import javafx.print.PrinterAttributes;
import javafx.print.PrinterJob;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.image.Image;
import javafx.scene.input.MouseEvent;
import javafx.scene.transform.Scale;
import javafx.stage.Stage;
import org.apache.poi.hssf.usermodel.HSSFRow;
import org.apache.poi.hssf.usermodel.HSSFSheet;
import org.apache.poi.hssf.usermodel.HSSFWorkbook;
import services.UserService;
import tools.MaConnection;

/**
 * FXML Controller class
 *
 * @author ASUS
 */
public class AfficheAdminController implements Initializable {

    @FXML
    private TableView<User> userTable;
    @FXML
    private TableColumn<User, String> idColumn,nomColumn,prenomColumn,emailColumn,roleColumn,genreColumn,passwordColumn,date_naissanceColumn,is_verifiedColumn;
   @FXML
    private TextField eChercher;
    @FXML
    private Button addButton;
    @FXML
    private Button editButton;
    @FXML
    private Button deleteButton;
/***************************************************/
    public User u;
    public List<User> users;
    private UserService us=new UserService();
    @FXML
    private Button xl;
    @FXML
    private ComboBox<String> ExporterListe;
/******************************************************/

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        refreshTable();
    }    
        private void refreshTable(){
        
        ObservableList<User> userlist = FXCollections.observableArrayList();
        idColumn.setCellValueFactory(new PropertyValueFactory<User,String>("id"));
        nomColumn.setCellValueFactory(new PropertyValueFactory<User,String>("nom"));
        prenomColumn.setCellValueFactory(new PropertyValueFactory<User,String>("prenom"));
        emailColumn.setCellValueFactory(new PropertyValueFactory<User,String>("email"));
        roleColumn.setCellValueFactory(new PropertyValueFactory<User,String>("roles"));
        genreColumn.setCellValueFactory(new PropertyValueFactory<User,String>("genre"));
        passwordColumn.setCellValueFactory(new PropertyValueFactory<User,String>("password"));
        date_naissanceColumn.setCellValueFactory(new PropertyValueFactory<User,String>("date_naissance"));
        is_verifiedColumn.setCellValueFactory(new PropertyValueFactory<User,String>("is_verified"));
        
        users=us.getAllUsers();
        userlist.addAll(users);
        userTable.setItems(userlist);
      
    }

    @FXML
    private void handleAdd(MouseEvent event) {
    }

    @FXML
    private void handleAdd(ActionEvent event) {
    }

    @FXML
    private void handleEdit(ActionEvent event) {
    }

    @FXML
    private void handleDelete(ActionEvent event) {
    }
  /**********************API Exporter to XL***********************************************************************/
    @FXML
    private void exporterToXlxs(MouseEvent event) {
        Connection cnx = MaConnection.getInstance().getCnx();
                try {
            String query = "select * from user";
            Statement st = cnx.createStatement();
            ResultSet rs = st.executeQuery(query);

            HSSFWorkbook wb = new HSSFWorkbook();
            HSSFSheet sheet = wb.createSheet("Liste des utilisateurs");

            HSSFRow header = sheet.createRow(0);

            header.createCell(0).setCellValue("id");
            header.createCell(1).setCellValue("nom");
            header.createCell(2).setCellValue("prenom");
            header.createCell(3).setCellValue("email");
            header.createCell(4).setCellValue("roles");
            header.createCell(5).setCellValue("genre");
            header.createCell(6).setCellValue("password");
            header.createCell(7).setCellValue("date_naissance");
            header.createCell(8).setCellValue("is_verified");

            int index = 1;
            while (rs.next()) {
                HSSFRow row = sheet.createRow(index);
                row.createCell(0).setCellValue(rs.getString(0));
                row.createCell(1).setCellValue(rs.getString(1));
                row.createCell(2).setCellValue(rs.getString(2));
                row.createCell(3).setCellValue(rs.getString(3));
                row.createCell(4).setCellValue(rs.getString(4));
                row.createCell(5).setCellValue(rs.getString(5));
                row.createCell(6).setCellValue(rs.getString(6));
                row.createCell(7).setCellValue(rs.getString(7));
                row.createCell(8).setCellValue(rs.getString(8));
                
               
                index++;

            }

            FileOutputStream fileOut = new FileOutputStream("users.xls");

            wb.write(fileOut);
            fileOut.close();

            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("INFORMATION");
            alert.setHeaderText("La table est bien été importé en xls ❗ ");

            alert.showAndWait();

        } catch (SQLException ex) {
            Logger.getLogger(AfficheAdminController.class.getName()).log(Level.SEVERE, null, ex);
        } catch (FileNotFoundException ex) {
            Logger.getLogger(AfficheAdminController.class.getName()).log(Level.SEVERE, null, ex);
        } catch (IOException ex) {
            Logger.getLogger(AfficheAdminController.class.getName()).log(Level.SEVERE, null, ex);
        }

    } 
/******************************************/
        @FXML
    private void ExporterListe(ActionEvent event) throws IOException, NoSuchMethodException, InstantiationException, InvocationTargetException, IllegalAccessException, SQLException {


            User f = userTable.getSelectionModel().getSelectedItem();
            Pdf pd = new Pdf();
            try {
                pd.GeneratePdf("" + f.getNom() + "", f, f.getId());
                Alert alert = new Alert(Alert.AlertType.INFORMATION);
                alert.setTitle("PDF");
                alert.setHeaderText(null);
                alert.setContentText("!!!PDF exported!!!");
                alert.showAndWait();
                System.out.println("impression done");
            } catch (Exception ex) {
                Logger.getLogger(UserService.class.getName()).log(Level.SEVERE, null, ex);
                Alert alert = new Alert(Alert.AlertType.WARNING);
                alert.setTitle("Alert");
                alert.setHeaderText(null);
                alert.showAndWait();
            }
        
    }

    private void PDF(MouseEvent event) {
        User f = userTable.getSelectionModel().getSelectedItem();

        Pdf pd = new Pdf();
        try {
            pd.GeneratePdf("MesInformations", f, f.getId());
            System.out.println("impression done");
        } catch (Exception ex) {
            Logger.getLogger(UserService.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    public static void printNode(final Node node) throws NoSuchMethodException, InstantiationException, IllegalAccessException, InvocationTargetException {
        Printer printer = Printer.getDefaultPrinter();
        javafx.print.PageLayout pageLayout = printer.createPageLayout(Paper.A4, PageOrientation.LANDSCAPE, Printer.MarginType.DEFAULT);
        PrinterAttributes attr = printer.getPrinterAttributes();
        PrinterJob job = PrinterJob.createPrinterJob();
        double scaleX = pageLayout.getPrintableWidth() / node.getBoundsInParent().getWidth();
        double scaleY = pageLayout.getPrintableHeight() / node.getBoundsInParent().getHeight();
        Scale scale = new Scale(scaleX, scaleY);
        node.getTransforms().add(scale);

        if (job != null && job.showPrintDialog(node.getScene().getWindow())) {
            boolean success = job.printPage(pageLayout, node);
            if (success) {
                job.endJob();

            }
        }
        node.getTransforms().remove(scale);

    }
@FXML
    private void afficherRec(ActionEvent event) {
     Stage stageclose=(Stage)((Node)event.getSource()).getScene().getWindow();
        stageclose.close();
        try {
            Parent root=FXMLLoader.load(getClass().getResource("/GUI/FXMLreclamationadmin.fxml"));

            Scene scene = new Scene(root);
            Stage primaryStage=new Stage();
            primaryStage.setTitle("liste des recalamation");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (IOException ex) {
            Logger.getLogger(FXMain.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    @FXML
private void afficher_evnt(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
    stage.setTitle("Gestion Evenements");
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/EventInterface.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}
    @FXML
private void stock(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
    stage.setTitle("Gestion des stocks");
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/location_article.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}
}
    
/*private void chercherparnom(ActionEvent event) {
    UserService rs = new UserService();
    ObservableList<User> list = FXCollections.observableList(rs.getAllUsers());
    UserService e = new UserService();

  // Set up the table view and its cell factories
nomColumn.setCellValueFactory(new PropertyValueFactory<>("nom"));
prenomColumn.setCellValueFactory(new PropertyValueFactory<>("prenom"));

userTable.setItems(list);

// Create a filtered list and bind it to the table view
FilteredList<User> filteredData = new FilteredList<>(list, b -> true);
userTable.setItems(filteredData);

// Create a sorted list and bind it to the filtered list
SortedList<User> sortedData = new SortedList<>(filteredData);
sortedData.comparatorProperty().bind(userTable.comparatorProperty());
userTable.setItems(sortedData);

// Set up the text field listener
eChercher.textProperty().addListener((observable, oldValue, newValue) -> {
    filteredData.setPredicate(reclamation -> {
        if (newValue == null || newValue.isEmpty()) {
            return true;
        }

        String lowerCaseFilter = newValue.toLowerCase();

        if (reclamation.getNom().toLowerCase().contains(lowerCaseFilter)) {
            return true;
        }

        return false;
    });
}*/
        
