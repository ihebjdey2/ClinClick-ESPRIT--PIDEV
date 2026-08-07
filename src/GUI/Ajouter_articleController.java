/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import entity.Article;
import Services.CRUDArticle;
import tools.MaConnection;
import java.io.IOException;
import java.net.URL;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.time.LocalDate;
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
import javafx.scene.control.TextField;
import javafx.scene.control.DatePicker;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.ComboBox;
import javafx.stage.Stage;
import static javax.management.remote.JMXConnectorFactory.connect;


/**
 * FXML Controller class
 *
 * @author ismae
 */
public class Ajouter_articleController implements Initializable {

    @FXML
    private TextField fx_nomA; 
    @FXML
    private TextField fx_catId;
    @FXML
    private ComboBox<?> fx_catId1;
    @FXML
    private TextField fx_quantiteA;
    @FXML
    private DatePicker fx_dateA;
    @FXML
    private Button ajouter;
    @FXML
    private Button annuler;
    

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
                Statement ste;
    Connection conn = MaConnection.getInstance().getCnx();
    String req1 = "SELECT CatId FROM categorie";

    try {
            ObservableList listF = FXCollections.observableArrayList();
            
            PreparedStatement prepare = conn.prepareStatement(req1);
            ResultSet result = prepare.executeQuery();
            while (result.next()) {
                listF.add(result.getInt("CatId"));
            }
            fx_catId1.setItems(listF);

        } catch (Exception e) {
            e.printStackTrace();
        }

    }    
    


    
    @FXML
    private void ajouter(ActionEvent event) throws IOException {

        String nomA = fx_nomA.getText();
        LocalDate date1 = fx_dateA.getValue();
        java.util.Date date = Date.valueOf(date1);
//        Parent page1 =
//        FXMLLoader.load(getClass().getResource("ajouter_article.fxml"));
//        Scene scene= new Scene(page1);
//        // get the ComboBox by its FX ID
//        ComboBox<String> comboBox = (ComboBox<String>) scene.lookup("#fx_catId1");
//        // get the Scene object
//        scene = comboBox.getScene();
//        System.out.println(comboBox);
//        // get the selected value from the ComboBox
//        String selectedValue = comboBox.getValue();
//        System.out.println(selectedValue);
//        // convert the selected value to an int
//        int CatId = Integer.parseInt(selectedValue);
        int CatId = Integer.parseInt(String.valueOf(fx_catId1.getValue().toString().charAt(0)));
//        int CatId = Integer.parseInt((String) fx_catId1.getSelectionModel().getSelectedItem());

        int quantiteA = Integer.parseInt(fx_quantiteA.getText());
    
            

        if(quantiteA<0){
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Information Dialog");
            alert.setHeaderText(null);
            alert.setContentText("erreur la quantite est negative");
            alert.show();
        }else if(nomA.length()==0){
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Information Dialog");
            alert.setHeaderText(null);
            alert.setContentText("erreur donner un nom d article");
            alert.show();
        }else if (date == null) {
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Information Dialog");
            alert.setHeaderText(null);
            alert.setContentText("Erreur: veuillez sélectionner une date.");
            alert.show();
        }/*else if (CatId!=(1,2,3){
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
           alert.setTitle("Information Dialog");
        alert.setHeaderText(null);
        alert.setContentText("erreur donner un catID");
        alert.show();
        }*/else {
            LocalDate date2 = fx_dateA.getValue();

            Article A = new Article(nomA, quantiteA, date2, CatId);
            CRUDArticle crud = new CRUDArticle();
            crud.ajouterArticle(A);
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Information Dialog");
            alert.setHeaderText(null);
            alert.setContentText("Article insérée avec succés!");
            alert.show();
        }
    }    
    
    @FXML
    private void annuler(ActionEvent event) {
         try {
            Parent page1
                    = FXMLLoader.load(getClass().getResource("crud_article.fxml"));
            Scene scene = new Scene(page1);
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException ex) {
            Logger.getLogger(Location_articleController.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
}
