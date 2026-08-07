/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import entity.User;
import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.event.Event;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import services.UserService;
import services.metiersUser.SessionUser;
import static tools.PasswordHashing.hashPassword;

/**
 * FXML Controller class
 *
 * @author ASUS
 */
public class LoginController implements Initializable {
/*************************************/
    User u = null;
    UserService us = new UserService();
    Stage stage;
    private Scene scene;
    Parent root;
/**************************************/
    @FXML
    private Label errorLabel;
    @FXML
    private Button loginButton;
    @FXML
    private Button signupButton;
    @FXML
    private TextField tfmail;
    @FXML
    private PasswordField tfmdp;

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
    }
    

    @FXML
private void handleLoginAction(ActionEvent event) throws IOException, SQLException {

    String mail = tfmail.getText();
    String mdp = hashPassword(tfmdp.getText());

    if (mail.equals("") || mdp.equals("")) {
        showAlert(Alert.AlertType.ERROR, "Données erronés", "Verifier les données", "Veuillez bien renseigner tous les champs !");
    } else {
        User user = us.getUserByMail(mail);
        if (user != null && user.getPassword().equals(mdp)) {
            User.setCurrent_User(user);
            SessionUser.getInstace(user.getId(), user.getEmail(), user.getRoles(), user.getPassword(), user.getIs_verified(), user.getDate_naissance(), user.getNom(), user.getPrenom(), user.getGenre());

            FXMLLoader loader = null;
            Parent root = null;
            Scene scene = null;
            Stage stage = null;

            String roles = user.getRoles();
            if (roles.equals("Admin")) {
                loader = new FXMLLoader(getClass().getResource("AfficheAdmiin.fxml"));
                root = loader.load();
                AfficheAdminController controller = loader.getController();
                stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            } else if (roles.equals("Medecin")) {
                loader = new FXMLLoader(getClass().getResource("Home.fxml"));
                root = loader.load();
                HomeController controller = loader.getController();
                stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            } else if (roles.equals("Patient")) {
                loader = new FXMLLoader(getClass().getResource("home.fxml"));
                root = loader.load();
                HomeController controller = loader.getController();
                stage = new Stage();
            } else {
                showAlert(Alert.AlertType.ERROR, "Erreur de connexion", "Erreur de connexion", "Impossible de déterminer le rôle de l'utilisateur.");
            }

            if (loader != null && root != null && stage != null) {
                scene = new Scene(root);
                stage.setScene(scene);
                stage.show();
            }

        } else {
            showAlert(Alert.AlertType.ERROR, "Données erronées", "Vérifier les données", "Nom d'utilisateur ou mot de passe incorrect.");
        }
    }
}


   
   @FXML
private void handleSignupAction(ActionEvent event) {
    
    
    System.out.print("hi");
    try {
        // Charger le fichier FXML de la page d'inscription
        FXMLLoader loader = new FXMLLoader(getClass().getResource("Inscription.fxml"));
        Parent root = loader.load();
        // Créer un nouveau stage pour la fenêtre d'inscription
        Stage stage = new Stage();
        Scene scene = new Scene(root);
        stage.setScene(scene);
        stage.setTitle("Inscription");
        stage.show();
       
    } catch (IOException e) {
    }
}
///**************************************************************************************************/
 public static void showAlert(Alert.AlertType type, String title, String header, String text) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(text);
        alert.showAndWait();

    }   
 
}
