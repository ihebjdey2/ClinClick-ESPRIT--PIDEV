/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import com.google.zxing.BarcodeFormat;
import com.google.zxing.WriterException;
import com.google.zxing.common.BitMatrix;
import com.google.zxing.qrcode.QRCodeWriter;
import com.google.zxing.client.j2se.MatrixToImageWriter;
import java.awt.image.BufferedImage;
import java.io.IOException;
import java.nio.file.Path;
import javafx.embed.swing.SwingFXUtils;
import javafx.fxml.FXML;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import com.google.zxing.WriterException;
import com.google.zxing.qrcode.QRCodeWriter;
import com.google.zxing.common.BitMatrix;
import com.google.zxing.client.j2se.MatrixToImageWriter;
import java.io.IOException;
import java.nio.file.FileSystems;
import java.nio.file.Path;
import java.io.FileInputStream;
import java.net.URL;
import java.nio.file.Paths;
import java.util.Date;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.time.LocalDate;
import static java.time.temporal.TemporalQueries.localDate;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.ResourceBundle;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.DateCell;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Label;
import javafx.scene.chart.PieChart;
import javafx.scene.layout.StackPane;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableRow;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.input.MouseEvent;
import javafx.scene.layout.StackPane;
import javafx.stage.Stage;
import javafx.util.Callback;
import entity.Rdv;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import services.RdvService;
import tools.MaConnection;

/**
 *
 * @author lengu
 */
public class RdvController implements Initializable {
    
    @FXML
    private Label label;
    @FXML
    private TableView<Rdv> tvrdv;
    @FXML
    private TableColumn<Rdv, String> colid;
    @FXML
    private TableColumn<Rdv, String> colnom;
    @FXML
    private TableColumn<Rdv, String> coldate;
    @FXML
    private Button btnadd;
    @FXML
    private Button btnupdate;
    @FXML
    private Button btndelete;
    @FXML
    private TextField tfid ;
    @FXML
    private TextField tfnom;
    @FXML
    private DatePicker dpdate;
    
    Connection connection = null;
    ObservableList<Rdv> rdv = FXCollections.observableArrayList();
    @FXML
    private ImageView img1;
    @FXML
    private ImageView img2;
    @FXML
    private ImageView img3;
    @FXML
    private ImageView img4;
    @FXML
    private TextField tfsearch;
    @FXML
    private Button btnsearch;
    @FXML
    private Button btnstat;
    @FXML
    private Button btnqrcode;
    
  
    
    @Override
    public void initialize(URL url, ResourceBundle rb) {
       viewrdv();
       coldate.setCellValueFactory(new PropertyValueFactory<>("date"));
    }   
    
   
 

    @FXML
    private void add(ActionEvent event) {
        //int id_rdv = Integer.parseInt(tfid.getText());
        String nom_rdv = tfnom.getText();
        String date = dpdate.getValue().toString();
        
        if (tfnom.getText().length() < 1) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("Erreur");
        alert.setHeaderText(null);
        alert.setContentText("VEUILLEZ REMPLIR VOTRE NOM");
        alert.showAndWait();
        return;
    } else{}  
        Rdv r = new Rdv(0, nom_rdv, date);
        RdvService rs = new RdvService();
        rs.ajouterRdv(r);
        getRdv();
    }
        

    @FXML
    private void update(ActionEvent event) {
        int id_rdv = Integer.parseInt(tfid.getText());
        String nom_rdv = tfnom.getText();
        String date = dpdate.getValue().toString();
        
        Rdv r = new Rdv(id_rdv, nom_rdv, date);
        RdvService rs = new RdvService();
        rs.modifierRdv(r);
                getRdv();

    }

    @FXML
    private void delete(ActionEvent event) {
        int id_rdv = Integer.parseInt(tfid.getText());
        RdvService rs = new RdvService();
        rs.supprimerRdv(id_rdv);
                getRdv();
                

    }

    private void getRdv() {
    try {
        rdv.clear();

        String req = "SELECT * FROM `rdv`";
        Statement ste = connection.createStatement();
        ResultSet result = ste.executeQuery(req);

        while (result.next()) {
            Rdv resultRdv;
            resultRdv = new Rdv(
                    result.getInt("id"),
                    result.getString("nom"),
                    result.getString("date"));
            rdv.add(resultRdv);
        }    
        // Tri des rendez-vous par date croissante
        Collections.sort(rdv, Comparator.comparing(r -> LocalDate.parse(r.getDate())));
        tvrdv.setItems(rdv);

    } catch (SQLException ex) {
        System.out.println(ex);
    }
}


    @FXML
private void viewrdv(){
    connection = MaConnection.getInstance().getCnx();
    getRdv();
    colid.setCellValueFactory(new PropertyValueFactory<>("id"));
    colnom.setCellValueFactory(new PropertyValueFactory<>("nom"));
    coldate.setCellValueFactory(new PropertyValueFactory<>("date"));
    
    // Tri des rendez-vous par date croissante
    Collections.sort(rdv, Comparator.comparing(r -> LocalDate.parse(r.getDate())));
    tvrdv.setItems(rdv);

    tvrdv.setRowFactory(tv-> {
        TableRow<Rdv> row = new TableRow<>();
        row.setOnMouseClicked(even -> {
            if (even.getClickCount() == 1 && !row.isEmpty()){
                int myIndex = tvrdv.getSelectionModel().getSelectedIndex();
                tfid.setText(""+(tvrdv.getItems().get(myIndex).getId()));
                tfnom.setText((tvrdv.getItems().get(myIndex).getNom()));
                LocalDate date = LocalDate.parse(tvrdv.getItems().get(myIndex).getDate());
                dpdate.setValue(date);
            }
        });
        return row;
    });
}


    @FXML
    private void search(ActionEvent event) {
        String nom = tfsearch.getText();
        RdvService rs = new RdvService();
        List<Rdv> rdvs = rs.chercherRdvParNom(nom);
        rdv.clear();
        rdv.addAll(rdvs);
        tvrdv.setItems(rdv);
    }

    @FXML
private void showStatistics() {
    ObservableList<PieChart.Data> pieChartData = FXCollections.observableArrayList();
    
    // Compter le nombre de RDV par mois
    Map<String, Integer> rdvByMonth = new HashMap<>();
    for (Rdv r : rdv) {
        String month = r.getDate().substring(5, 7);
        rdvByMonth.put(month, rdvByMonth.getOrDefault(month, 0) + 1);
    }
    
    // Ajouter les données au graphique en camembert
    for (String month : rdvByMonth.keySet()) {
        pieChartData.add(new PieChart.Data("Mois " + month, rdvByMonth.get(month)));
    }
    
    // Créer le graphique en camembert et l'afficher
    PieChart chart = new PieChart(pieChartData);
    chart.setTitle("Statistiques des RDV ajoutés");
    StackPane pane = new StackPane(chart);
    Scene scene = new Scene(pane, 800, 600);
    Stage stage = new Stage();
    stage.setScene(scene);
    stage.show();
}

    @FXML
private void genererqrcode(ActionEvent event) {
    // Get the selected rdv
    Rdv selectedRdv = tvrdv.getSelectionModel().getSelectedItem();
    
    // Check if an rdv is selected
    if (selectedRdv == null) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("Erreur");
        alert.setHeaderText(null);
        alert.setContentText("VEUILLEZ SELECTIONNER UN RDV");
        alert.showAndWait();
        return;
    }
    
    // Generate QR code data
    String data = "RDV: " + selectedRdv.getId() + "\n" + "Nom: " + selectedRdv.getNom() + "\n" + "Date: " + selectedRdv.getDate();
    
    // Set up QR code writer
    QRCodeWriter writer = new QRCodeWriter();
    int size = 250;
    BitMatrix bitMatrix = null;
    
    try {
        // Generate QR code
        bitMatrix = writer.encode(data, BarcodeFormat.QR_CODE, size, size);
    } catch (WriterException e) {
        e.printStackTrace();
    }
    
    // Convert QR code to image
    BufferedImage image = MatrixToImageWriter.toBufferedImage(bitMatrix);
    Image fxImage = SwingFXUtils.toFXImage(image, null);
    
    // Set the image in the ImageView
    img1.setImage(fxImage);
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
 
 
        
       
