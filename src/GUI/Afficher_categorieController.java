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
import javafx.stage.Stage;





import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.TableColumn.CellEditEvent;
import javafx.scene.control.TextField;
/**

/**
 * FXML Controller class
 *
 * @author ismae
 */
public class Afficher_categorieController implements Initializable {

    @FXML
    private ListView<Categorie> affichercategorie;
    @FXML
    private Button supprimer;
    @FXML
    private Button mod;
    @FXML
    private Button annuler;
    
    
        static String CatLib;
        static int CatId;


    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        ListView<Categorie> list1= affichercategorie;
        CRUDCategorie inter = new CRUDCategorie();
        List<Categorie> list2 = inter.afficherCategorie();
        for (int i = 0; i < list2.size(); i++) {
            Categorie A = list2.get(i);
            list1.getItems().add(A);

        }    }    
    

    @FXML
    private void supprimer_categorie(ActionEvent event) {
        ListView<Categorie> list1= affichercategorie;
        CRUDCategorie inter = new CRUDCategorie();
        int selectedIndex = list1.getSelectionModel().getSelectedIndex();
        if (selectedIndex >= 0) {
            Categorie A = list1.getSelectionModel().getSelectedItem();
            System.out.println(A.getCatId());
            inter.supprimerCategorie(A.getCatId());
            list1.getItems().remove(selectedIndex);
        } else {
            System.out.println("Veuillez sélectionner un categorie à supprimer.");
        }
    }

    @FXML
    private void mod(ActionEvent event) {
            ListView<Categorie> list = affichercategorie;
        CRUDCategorie inter = new CRUDCategorie();
        int selectedIndex = list.getSelectionModel().getSelectedIndex();
        Categorie A = list.getSelectionModel().getSelectedItem();
        CatId=A.getCatId();
        CatLib= A.getCatLib();


        try {

            Parent page1
                    = FXMLLoader.load(getClass().getResource("modifiercategorie.fxml"));
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
    


    
    
  
  