/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import entity.Article;

import Services.CRUDArticle;
import Utils.sms;
import java.io.IOException;
import java.net.URL;
import java.time.LocalDate;
import java.util.List;
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
import javafx.stage.Stage;

import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.TableColumn.CellEditEvent;
import javafx.scene.control.TextField;
import java.io.ByteArrayOutputStream;
import java.util.HashMap;
import java.util.Map;

import javafx.application.Application;
import javafx.embed.swing.SwingFXUtils;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import javax.imageio.ImageIO;


import javafx.scene.image.Image;
import javafx.scene.image.ImageView;

import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.VBox;

import java.net.URL;
import java.util.ResourceBundle;





/**
 * FXML Controller class
 *
 * @author ismae
 */
public class Afficher_articleController implements Initializable {


    @FXML
    private ListView<Article> afficherarticle;
    @FXML
    private Button supprimer;
    @FXML
    private Button mod;
    @FXML
    private Button annuler;
    @FXML
    private Button actualiser;
    
    static int refA;
    static String nomA;
    static int quantiteA;
    static int CatId;
    static LocalDate date;
    
    
  
    


    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        ListView<Article> list1 = afficherarticle;
        CRUDArticle inter = new CRUDArticle();
        List<Article> list2 = inter.afficherArticle();
        for (int i = 0; i < list2.size(); i++) {
            Article A = list2.get(i);
            list1.getItems().add(A);

        }    }
    
    /*@FXML

    public void initialize1(URL location, ResourceBundle resources) {
        code.setOnAction(event -> {
            String content = String.format("ref:%s,nom:%s,quantite:%d,date:%s,catId:%d",
                refA, nomA, quantiteA, date, CatId);
            Image qrImage = QRCode.from(content).to(ImageType.PNG).withSize(250, 250).image();
            ImageView qrImageView = new ImageView(qrImage);
            qrCodeContainer.getChildren().clear();
            qrCodeContainer.getChildren().add(qrImageView);
        });
    }
    */
@FXML
void actualiser(ActionEvent event) {
    ListView<Article> list2 = afficherarticle;
    CRUDArticle inter = new CRUDArticle();
    List<Article> articles = inter.actualiserArticle(); // get the updated list of articles
    list2.getItems().clear(); // clear the displayed list
    list2.getItems().addAll(articles); // add the updated articles to the list
            List<Article> list3 = inter.afficherArticle();
        for (int i = 0; i < list3.size(); i++) {
            Article A = list3.get(i);
            list2.getItems().add(A);
            
        }
}

            

    @FXML
    private void supprimer_article(ActionEvent event) {
    ListView<Article> list1 = afficherarticle;
        CRUDArticle inter = new CRUDArticle();
        int selectedIndex = list1.getSelectionModel().getSelectedIndex();
        if (selectedIndex >= 0) {
            Article A = list1.getSelectionModel().getSelectedItem();
            System.out.println(A.getRef());
            inter.supprimerArticle(A.getRef());
            list1.getItems().remove(selectedIndex);
            sms.sendsms(A.getRef());
        } else {
            System.out.println("Veuillez sélectionner un article à supprimer.");
        }

        


    }
    


    
    
    @FXML
    private void mod(ActionEvent event) {
    ListView<Article> list = afficherarticle;
        CRUDArticle inter = new CRUDArticle();
        int selectedIndex = list.getSelectionModel().getSelectedIndex();
        Article A = list.getSelectionModel().getSelectedItem();
        refA=A.getRef();
        nomA= A.getNom();
        quantiteA=A.getQuantite();
        CatId=A.getCategorieId();
        date=A.getDate();
        
        try {
            
            Parent page1
                    = FXMLLoader.load(getClass().getResource("modifierarticle.fxml"));
            Scene scene = new Scene(page1);
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException ex) {
            Logger.getLogger(Location_articleController.class.getName()).log(Level.SEVERE, null, ex);
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
    
