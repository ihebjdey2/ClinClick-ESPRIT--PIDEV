/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import entity.Article;
import entity.Categorie;
import Services.CRUDArticle;
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
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;

import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.scene.input.MouseEvent;
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author ismae
 */
public class ModifiercategorieController implements Initializable {
    private ListView<Categorie> affichercategorie;

    @FXML
    private TextField fx_nom;
    @FXML
    private Button modifier_categorie;
    @FXML
    private Button annuler;
    

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
 fx_nom.setText(Afficher_categorieController.CatLib);
    }    

    @FXML
    private void modifier_categorie(ActionEvent event) {
          CRUDCategorie inter = new CRUDCategorie();
        String CatLib = fx_nom.getText();        
      
        Categorie A = new Categorie(Afficher_articleController.CatId,CatLib);
        inter.modifierCategorie(A);
    }
    
    
    @FXML
    private void annuler(ActionEvent event) {
         try {

            Parent page1
                    = FXMLLoader.load(getClass().getResource("Afficher_categorie.fxml"));
            Scene scene = new Scene(page1);
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException ex) {
            Logger.getLogger(Location_articleController.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
}


