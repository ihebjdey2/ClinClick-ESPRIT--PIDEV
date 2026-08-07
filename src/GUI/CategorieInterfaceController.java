/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import com.itextpdf.text.DocumentException;
import java.io.FileNotFoundException;
import entity.Category;
import Services_event.ServiceCategorie;
import tools.MaConnection;
import java.io.IOException;
import java.net.URL;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
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
import javafx.scene.chart.BarChart;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.TextField;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.control.cell.TextFieldTableCell;
import javafx.stage.Stage;
import tray.notification.NotificationType;
import tray.notification.TrayNotification;

/**
 * FXML Controller class
 *
 * @author msi
 */
public class CategorieInterfaceController implements Initializable {

    @FXML
    private TableView<Category> tableCategorie;
    @FXML
    private TableColumn<Category, Integer> idt;
    @FXML
    private TableColumn<Category, String> nomT;
    @FXML
    private TableColumn<Category, String> libelleT;

    private Statement ste;
    private Connection con;
    private final ObservableList<Category> data = FXCollections.observableArrayList();
    
    ServiceCategorie sc = new ServiceCategorie();
    @FXML
    private TextField inputNom;
    @FXML
    private TextField inputLibelle;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {

        try {
            Aff();
                    } catch (SQLException ex) {
            Logger.getLogger(CategorieInterfaceController.class.getName()).log(Level.SEVERE, null, ex);
        }
    }    

            
    private boolean Validchamp(TextField T){
        return !T.getText().isEmpty() && T.getLength() > 2;
    }

    @FXML
    private void Ajoutercategorie(ActionEvent event) throws SQLException {
       
        if(Validchamp(inputNom) && Validchamp(inputLibelle))
        {

        Category d = new Category(inputNom.getText(),inputLibelle.getText());

        sc.ajouter(d);        
        Aff();
        
        inputNom.setText("");
        inputLibelle.setText("");
        
        TrayNotification tray = new TrayNotification();
        tray.setTitle("Category "+ d.getNom()+" ajouté");
        tray.setMessage("Libellé: "+d.getLibelle());
        tray.setNotificationType(NotificationType.SUCCESS);
        tray.showAndWait();
        }
        else
        {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("ERROR");
        alert.setHeaderText(null);
        alert.setContentText("Verifiez les champs!");

        alert.showAndWait();
        }
    }

    @FXML
    private void Supprimer(ActionEvent event) throws SQLException {
             tableCategorie.setItems(data);

             ObservableList<Category> allCategories,SingleCategorie ;
             allCategories=tableCategorie.getItems();
             SingleCategorie=tableCategorie.getSelectionModel().getSelectedItems();
             Category A = SingleCategorie.get(0);
             sc.delete(A);
             SingleCategorie.forEach(allCategories::remove);
             Aff();
             
             TrayNotification tray = new TrayNotification();                        
                tray.setTitle("Category "+ A.getNom()+" Supprimer");
                tray.setMessage("Libellé: "+A.getLibelle());
                tray.setNotificationType(NotificationType.ERROR);
                tray.showAndWait();
    }

    public void Aff() throws SQLException {
                       
        try {
            con = MaConnection.getInstance().getCnx();    
            ste = con.createStatement();
            data.clear();

            ResultSet res = ste.executeQuery("select * from Category");
            while(res.next()){
                Category f= new Category(res.getInt(1),res.getString(2),res.getString(3));
                data.add(f);
            }

        } catch (Exception e) {
                //Logger.getLogger(tab)
        }
            idt.setCellValueFactory(new PropertyValueFactory<>("id"));
            nomT.setCellValueFactory(new PropertyValueFactory<>("nom"));
            libelleT.setCellValueFactory(new PropertyValueFactory<>("libelle"));
            
            tableCategorie.setItems(data);
            tableCategorie.setEditable(true);
            nomT.setCellFactory(TextFieldTableCell.forTableColumn());
            libelleT.setCellFactory(TextFieldTableCell.forTableColumn());
    }


    @FXML
    private void Change_Nom(TableColumn.CellEditEvent event) throws SQLException {
                
        Category tab_CategorieSelected = tableCategorie.getSelectionModel().getSelectedItem();
        tab_CategorieSelected.setNom(event.getNewValue().toString());
        sc.update(tab_CategorieSelected);
        
        TrayNotification tray = new TrayNotification();
                        
        tray.setTitle("Category "+ tab_CategorieSelected.getNom()+" Modifier");
        tray.setMessage("Nom modifié");
        tray.setNotificationType(NotificationType.WARNING);
        tray.showAndWait();

    }

    @FXML
    private void Change_Libelle(TableColumn.CellEditEvent event) throws SQLException {
                
        Category tab_CategorieSelected = tableCategorie.getSelectionModel().getSelectedItem();
        tab_CategorieSelected.setLibelle(event.getNewValue().toString());
        sc.update(tab_CategorieSelected);
        TrayNotification tray = new TrayNotification();
        tray.setTitle("Category "+ tab_CategorieSelected.getNom()+" Modifier");
        tray.setMessage("Libellé modifié");
        tray.setNotificationType(NotificationType.WARNING);
        tray.showAndWait();

    }


}
    