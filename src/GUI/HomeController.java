/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import gestionreclamationsante.FXMain;
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
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author ASUS
 */
public class HomeController implements Initializable {

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
    }    

    @FXML
    private void newPatient(ActionEvent event) {
    }

    @FXML
    private void newConsultation(ActionEvent event) {
    }

    @FXML
    private void newArticle(ActionEvent event) {
    }

    @FXML
    private void exit(ActionEvent event) {
    }

    @FXML
    private void about(ActionEvent event) {
    }

    @FXML
    private void addPatient(ActionEvent event) {
    }
    @FXML
    private void addReclalamtion(ActionEvent event) {
     Stage stageclose=(Stage)((Node)event.getSource()).getScene().getWindow();
        stageclose.close();
        try {
            Parent root=FXMLLoader.load(getClass().getResource("/GUI/FXMLreclamationuser.fxml"));

            Scene scene = new Scene(root);
            Stage primaryStage=new Stage();
            primaryStage.setTitle("faire une recalamation");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (IOException ex) {
            Logger.getLogger(FXMain.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
@FXML
private void afficher_evnt(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
    stage.setTitle("Gestion Evenements");
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/Menu.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}
    
@FXML
private void rendez_vous(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
    stage.setTitle("faire un rendezvous");
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/rdv.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}
    
}
