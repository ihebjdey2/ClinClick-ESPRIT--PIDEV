/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import entity.Article;
import entity.Categorie;
import Services.CRUDArticle;
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
import java.time.LocalDate;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.TableColumn.CellEditEvent;
import javafx.scene.control.TextField;
import javafx.scene.control.TreeItem;
import javafx.scene.control.TreeTableCell;
import javafx.scene.control.TreeTableColumn;
import javafx.scene.control.TreeTableView;
import javafx.scene.control.cell.TreeItemPropertyValueFactory;
/**
 * FXML Controller class
 *
 * @author ismae
 */
public class Afficher_article2Controller implements Initializable {

    @FXML
    private TreeTableView<Article> treeTableView;
    @FXML
    private TreeTableColumn<Article, Integer> ref;
    @FXML
    private TreeTableColumn<Article, String> nom;
    @FXML
    private TreeTableColumn<Article, Categorie> categorie;
    @FXML
    private TreeTableColumn<Article, Integer> quantite;
    @FXML
    private TreeTableColumn<Article, LocalDate> date;
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
    static LocalDate dateA;
    
    
  
    


    /**
     * Initializes the controller class.
     */
    @Override
   public void initialize(URL url, ResourceBundle rb) {
    // Set up the columns
    ref.setCellValueFactory(new TreeItemPropertyValueFactory<>("refA"));
    nom.setCellValueFactory(new TreeItemPropertyValueFactory<>("nomA"));
    categorie.setCellValueFactory(new TreeItemPropertyValueFactory<>("CatId"));
    quantite.setCellValueFactory(new TreeItemPropertyValueFactory<>("quantiteA"));
    date.setCellValueFactory(new TreeItemPropertyValueFactory<>("dateA"));
    
    // Set up the tree table view
    TreeItem<Article> rootItem = new TreeItem<>();
    treeTableView.setRoot(rootItem);
    treeTableView.setShowRoot(false);
    
    // Populate the tree table view with data
    CRUDArticle inter = new CRUDArticle();
    List<Article> articles = inter.afficherArticle();
    for (Article article : articles) {
        TreeItem<Article> articleItem = new TreeItem<>(article);
        rootItem.getChildren().add(articleItem);
    }
}
    
@FXML
void actualiser(ActionEvent event) {
    TreeItem<Article> root = treeTableView.getRoot();
    CRUDArticle inter = new CRUDArticle();
    List<Article> articles = inter.actualiserArticle(); // get the updated list of articles
    root.getChildren().clear(); // clear the displayed list
    for (Article article : articles) {
        TreeItem<Article> item = new TreeItem<>(article);
        root.getChildren().add(item); // add the updated article to the tree
    }
}


            

    @FXML
    public void supprimer_article() {
        TreeItem<Article> selectedItem = treeTableView.getSelectionModel().getSelectedItem();
        if (selectedItem != null) {
            int ref = selectedItem.getValue().getRef();
            CRUDArticle articleDAO = new CRUDArticle();
            articleDAO.supprimerArticle(ref);
            System.out.println("Article deleted!");
        }
    }




    

    @FXML
private void mod(ActionEvent event) {
    
    
    
    TreeItem<Article> selectedItem = treeTableView.getSelectionModel().getSelectedItem();
    if (selectedItem != null) {
        Article article = selectedItem.getValue();
        refA = article.getRef();
        nomA = article.getNom();
        quantiteA = article.getQuantite();
        CatId = article.getCategorieId();
TreeTableColumn<Article, LocalDate> dateCol = new TreeTableColumn<>("dateA");
dateCol.setCellValueFactory(new TreeItemPropertyValueFactory<>("dateA"));
dateCol.setCellFactory(column -> new TreeTableCell<Article, LocalDate>() {
    @Override
    protected void updateItem(LocalDate date, boolean empty) {
        super.updateItem(date, empty);
        if (empty || date == null) {
            setText(null);
        } else {
            setText(date.toString()); // Or format the date as you prefer
        }
    }
});

        try {
            Parent page1 = FXMLLoader.load(getClass().getResource("modifierarticle.fxml"));
            Scene scene = new Scene(page1);
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            stage.setScene(scene);
            stage.show();
        } catch (IOException ex) {
            Logger.getLogger(Afficher_article2Controller.class.getName()).log(Level.SEVERE, null, ex);
        }
    } else {
        System.out.println("Veuillez sélectionner un article à modifier.");
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
    
