/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import com.itextpdf.text.Document;
import java.io.IOException;
import java.net.URI;
import org.apache.http.HttpEntity;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;
import org.jsoup.Jsoup;
import javafx.scene.control.Alert;
import entity.Reclamation;
import java.io.IOException;
import java.net.URL;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import java.util.Optional;
import java.util.ResourceBundle;
import java.util.logging.Level;
import java.util.logging.Logger;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.scene.control.Dialog;
import javafx.scene.control.Slider;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.input.MouseEvent;
import javafx.stage.Stage;
import services.ServiceReclamation;
import tools.MaConnection;


/**
 * FXML Controller class
 *
 * @author Skymil
 */
public class FXMLreclamationuserController implements Initializable {

    @FXML
    private TableView<Reclamation> tvreclamation;
    @FXML
    private TableColumn<Reclamation, String> tcemail;
    @FXML
    private TableColumn<Reclamation, String> tcnom;
    @FXML
    private TableColumn<Reclamation, String> tcdescription;
    
    @FXML
    private TableColumn<Reclamation, String> tcetat;
    @FXML
    private TextField tfnom;
    @FXML
     private TextField tfemail;
    @FXML
    private TextArea tfdescription;
    ObservableList<Reclamation> data=FXCollections.observableArrayList();
    ServiceReclamation sr=new ServiceReclamation();
 @FXML
private Button ratingButton;
    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
        refresh();
       
         ratingButton.setOnAction(this::getRating);
      
    }    

    @FXML
    private void envoyer(ActionEvent event) {
        Reclamation r=new Reclamation();
        r.setEmail("aaa@gmail.com");
        r.setDescription(tfdescription.getText());
        r.setEmail(tfemail.getText());
        r.setEtat(false);
        r.setNom(tfnom.getText());
         r.setRating(0);
        if(controleDeSaisie().length()>0){
            Alert alert=new Alert(Alert.AlertType.WARNING);
            alert.setTitle("Warning adding claim");
            alert.setContentText(controleDeSaisie());
            alert.showAndWait();
        }
        else{
            sr.ajouter(r);
            refresh();
        }
        
    }

    @FXML
    private void modifier(ActionEvent event) {
        Reclamation r=tvreclamation.getSelectionModel().getSelectedItem();
       
        if(r!=null){
            
           
            r.setDescription(tfdescription.getText());
            r.setEmail(tfemail.getText());
      
            r.setEtat(false);
            r.setNom(tfnom.getText());
            if(controleDeSaisie().length()>0){
                Alert alert=new Alert(Alert.AlertType.WARNING);
                alert.setTitle("Warning modifying claim");
                alert.setContentText(controleDeSaisie());
                alert.showAndWait();
            }
            else{
                sr.modifier(r.getId(),r);
                refresh();
            }
            
        }
        
    }

    @FXML
    private void supprimer(ActionEvent event) {
        Reclamation r=tvreclamation.getSelectionModel().getSelectedItem();
        if(r!=null){
            sr.supprimer(r.getId());
            refresh();
        }
    }
    public void refresh(){
        data.clear();
        data=FXCollections.observableArrayList(sr.afficherReclamationUser(tfemail.getText()));
        tcemail.setCellValueFactory(new PropertyValueFactory<>("email"));
        tcnom.setCellValueFactory(new PropertyValueFactory<>("nom"));
        tcetat.setCellValueFactory(new PropertyValueFactory<>("etat"));
        tcdescription.setCellValueFactory(new PropertyValueFactory<>("description"));
        tvreclamation.setItems(data);
    }
    public String controleDeSaisie(){
        String erreurs="";
        if(tfnom.getText().trim().isEmpty()){
            erreurs+="-Le champs objet est vide!\n";
        }
        if(tfdescription.getText().trim().isEmpty()){
            erreurs+="-Le champs description est vide!\n";
        }
        if(tfdescription.getText().trim().length()<10){
            erreurs+="-Le champs description doit etre > 10 charactere!\n";
        }
        return erreurs;
    }

   @FXML
private void fillforum(MouseEvent event) {
    Reclamation r = tvreclamation.getSelectionModel().getSelectedItem();
    if (r != null) {
        tfnom.setText(r.getNom());
        tfdescription.setText(r.getDescription());
        if (r.getEtat()) {
            try {Connection connection;
                // Retrieve the response from the database
                connection = MaConnection.getInstance().getCnx();
                PreparedStatement statement = connection.prepareStatement("SELECT message FROM reponse WHERE relation_reclamation_id = ?");
                statement.setInt(1, r.getId());
                ResultSet resultSet = statement.executeQuery();
                String responseText = "";
                if (resultSet.next()) {
                    responseText = resultSet.getString("message");
                }
                resultSet.close();
                statement.close();
               

                // Display the response in an alert dialog
                Alert alert = new Alert(Alert.AlertType.INFORMATION);
                alert.setTitle("Response");
                alert.setHeaderText("Response to Reclamation #" + r.getId());
                alert.setContentText(responseText);
                alert.showAndWait();
            } catch (SQLException ex) {
                Logger.getLogger(FXMLreclamationuserController.class.getName()).log(Level.SEVERE, null, ex);
                Alert alert = new Alert(Alert.AlertType.ERROR);
                alert.setTitle("Error");
                alert.setHeaderText("An error occurred while retrieving the response");
                alert.setContentText(ex.getMessage());
                alert.showAndWait();
            }
        }
    }
}

    

    
    
  @FXML
private void getRating(ActionEvent event) {
    Reclamation selectedReclamation = tvreclamation.getSelectionModel().getSelectedItem();
    if (selectedReclamation == null) {
        // No reclamation is selected, show an error message
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("Error");
        alert.setHeaderText("No reclamation selected");
        alert.setContentText("Please select a reclamation first.");
        alert.showAndWait();
        return;
    }
    if (!selectedReclamation.getEtat()) {
        // The selected reclamation is not in the "etat" state, show an error message
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("Error");
        alert.setHeaderText("Cannot rate reclamation because it has no response");
        alert.setContentText("This reclamation cannot be rated as it is not in the appropriate state.");
        alert.showAndWait();
        return;
    }

    // Show the rating control to the user
    Dialog<Integer> ratingDialog = new Dialog<>();
    ratingDialog.setTitle("Rate the reponse to your Reclamation");
    ratingDialog.setHeaderText("Rate the selected reclamation from 1 to 5:");
    ratingDialog.getDialogPane().getButtonTypes().addAll(ButtonType.OK, ButtonType.CANCEL);

    // Create a rating control and add it to the dialog
    Slider ratingSlider = new Slider(1, 5, 3);
    ratingSlider.setMajorTickUnit(1);
    ratingSlider.setMinorTickCount(0);
    ratingSlider.setSnapToTicks(true);
    ratingDialog.getDialogPane().setContent(ratingSlider);

    // Set the result converter for the dialog
    ratingDialog.setResultConverter(dialogButton -> {
        if (dialogButton == ButtonType.OK) {
            return (int) ratingSlider.getValue();
        }
        return null;
    });

    // Show the dialog and wait for the user to make a selection
    Optional<Integer> result = ratingDialog.showAndWait();
    if (result.isPresent()) {
        int rating = result.get();

        // Update the rating of the selected reclamation in the
selectedReclamation.setRating(rating);
   sr.modifier(selectedReclamation.getId(),selectedReclamation);

    // Show a success message
    Alert alert = new Alert(Alert.AlertType.INFORMATION);
    alert.setTitle("Success");
    alert.setHeaderText("Reclamation rated");
    alert.setContentText("The rating for the selected reclamation has been updated.");
    alert.showAndWait();
    }


}
@FXML
private void retour(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
   
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/home.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}}

//    @FXML
//    private void ajouter(ActionEvent event) {
//    }
//     @FXML
//     public static String replaceBadWords(String originalString, String badWordsFilePath) {
//        String cleanedString = originalString;
//        // Read bad words from file
//        List<String> badWords = null;
//        try {
//            badWords = Files.readAllLines(Paths.get(badWordsFilePath));
//        } catch (IOException ex) {
//            Logger.getLogger(FXMLreclamationadminController.class.getName()).log(Level.SEVERE, null, ex);
//        }
//        // Replace bad words with asterisks
//        for (String badWord : badWords) {
//            if (badWord != null && !badWord.isEmpty()) {
//                StringBuilder asterisksBuilder = new StringBuilder();
//                for (int i = 0; i < badWord.length(); i++) {
//                    asterisksBuilder.append("*");
//                }
//                String asterisks = asterisksBuilder.toString();
//                cleanedString = cleanedString.replaceAll("(?i)\\b" + badWord + "\\b", asterisks);
//            }
//        }
//        return cleanedString;
//    }
//}
