/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


package GUI;

import entity.Article;
import entity.Categorie;
import Services.CRUDArticle;
import static GUI.Afficher_articleController.date;
import java.io.IOException;
import java.net.URL;
import java.sql.Date;
import java.time.LocalDate;
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
//import javafx.scene.control.Button;
//import javafx.scene.control.ListView;
//import javafx.scene.control.TextField;


import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
//import javafx.scene.input.MouseEvent;
/**
 * FXML Controller class
 *
 * @author ismae
 */


public class ModifierarticleController implements Initializable {
    private ListView<Article> afficherarticle;

    @FXML
    private TextField fx_nom;
    @FXML
    private TextField fx_quantite;
    @FXML
    private TextField fx_catid;
    @FXML
    private Button mod;
    @FXML
    private Button annuler;
    

  
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        fx_nom.setText(Afficher_articleController.nomA);
        fx_quantite.setText(Integer.toString(Afficher_articleController.quantiteA));
        fx_catid.setText(Integer.toString(Afficher_articleController.CatId));
            }    

//    @FXML
//    private void mod(ActionEvent event) {  
//        CRUDArticle inter = new CRUDArticle();
//        String nomA = fx_nom.getText();
//        int quantiteA = Integer.parseInt(fx_quantite.getText());
//        int CatId = Integer.parseInt(fx_catid.getText());
//        Article A = new Article(Afficher_articleController.refA,nomA,quantiteA, date, CatId);
//        inter.modifierArticle(A);
//    }
    
        @FXML
    private void mod(ActionEvent event) {
          CRUDArticle inter = new CRUDArticle();
        String nomA = fx_nom.getText();
        int quantiteA = Integer.parseInt(fx_quantite.getText());
        int CatId = Integer.parseInt(fx_catid.getText());
        Article A = new Article(Afficher_articleController.refA,nomA,quantiteA, date, CatId);
        inter.modifierArticle(A);
    }
    
    
        @FXML
    private void annuler(ActionEvent event) {
         try {

            Parent page1
                    = FXMLLoader.load(getClass().getResource("Afficher_article2.fxml"));
            Scene scene = new Scene(page1);
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException ex) {
            Logger.getLogger(Location_articleController.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
}
