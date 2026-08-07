/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import entity.Categorie;
import Services.CRUDCategorie;
import java.io.IOException;
import java.net.URL;
import java.util.ResourceBundle;
import java.util.logging.Level;
import java.util.logging.Logger;
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
import javafx.scene.control.Alert.AlertType;
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author ismae
 */
public class Ajouter_categorieController implements Initializable {

    @FXML
    private TextField fx_nomA;
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
    }    

    @FXML
    private void ajouter(ActionEvent event) {
        String CatLib = fx_nomA.getText();
       
         if(CatLib.length()==0){
            Alert alert = new Alert(Alert.AlertType.INFORMATION);
            alert.setTitle("Information Dialog");
            alert.setHeaderText(null);
            alert.setContentText("erreur donner un libelle ");
            alert.show();
        }else {
            Categorie A = new Categorie(CatLib);
            CRUDCategorie crud = new CRUDCategorie();
            crud.ajouterCategorie(A);
            Alert alert = new Alert(Alert.AlertType.INFORMATION);

            alert.setTitle("Information Dialog");
            alert.setHeaderText(null);
            alert.setContentText("Categorie insérée avec succés!");
            alert.show();
    }
    }
    
        @FXML
    private void annuler(ActionEvent event) {
         try {

            Parent page1
                    = FXMLLoader.load(getClass().getResource("crud_categorie.fxml"));
            Scene scene = new Scene(page1);
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException ex) {
            Logger.getLogger(Location_articleController.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
    }
    


