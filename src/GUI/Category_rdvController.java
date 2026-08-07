/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import java.net.URL;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.List;
import java.util.ResourceBundle;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.control.SortEvent;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableRow;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;
import entity.category_rdv;
import services.Category_rdvService;
import tools.MaConnection;

/**
 * FXML Controller class
 *
 * @author lengu
 */
public class Category_rdvController implements Initializable {

    @FXML
    private TextField tfnomcat;
    @FXML
    private TableView<?> tvcat;
    @FXML
    private TableColumn<category_rdv, String> colnomcat;
    @FXML
    private Button btnaddcat;
    @FXML
    private Button btnsuppcat;
    @FXML
    private Button btnmodcat;
    @FXML
    private TextField tfrechcat;
    @FXML
    private Button btnrechcat;
    
    Connection connection = null;
    ObservableList<category_rdv> categories = FXCollections.observableArrayList();

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        viewcat();
    }    

    @FXML
    private void viewcat() {
        
    }

    @FXML
    private void addcat(ActionEvent event) {
          String nom_cat = tfnomcat.getText();
    category_rdv c = new category_rdv(nom_cat);
    Category_rdvService cs = new Category_rdvService();
    cs.ajouterCat(c);
    getcategory_rdv();
    }

    @FXML
    private void suppcat(ActionEvent event) { 
        category_rdv selectedcategory_rdv = (category_rdv) tvcat.getSelectionModel().getSelectedItem();
    Category_rdvService cs = new Category_rdvService();
    cs.supprimerCat(selectedcategory_rdv.getId());
    getcategory_rdv();
    }

    @FXML
    private void modcat(ActionEvent event) {
         category_rdv selectedcategory_rdv = (category_rdv) tvcat.getSelectionModel().getSelectedItem();
    String nom_cat = tfnomcat.getText();
    category_rdv c = new category_rdv(selectedcategory_rdv.getId(), nom_cat);
    Category_rdvService cs = new Category_rdvService();
    cs.modifierCat(c);
    getcategory_rdv();
    }

    @FXML
    private void rechcat(ActionEvent event) {
        String searchQuery = tfrechcat.getText();
    Category_rdvService cs = new Category_rdvService();
    List<category_rdv> searchResults = cs.chercherCat(searchQuery);
    categories.setAll(searchResults);
    }
    
    

    private void getcategory_rdv() {
       
    try {
        categories.clear();
        String req = "SELECT * FROM `category_rdv`";
        Statement ste = connection.createStatement();
        ResultSet result = ste.executeQuery(req);
        
        while (result.next()) {
            category_rdv resultcategory_rdv = new category_rdv(result.getString("nom")); 
            categories.add(resultcategory_rdv);             
        }

    } catch (SQLException ex) {
        System.out.println(ex);
    }
}

    
}
