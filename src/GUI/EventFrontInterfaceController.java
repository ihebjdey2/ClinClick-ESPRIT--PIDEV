/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package GUI;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.net.URL;
import java.sql.SQLException;
import java.util.List;
import java.util.ResourceBundle;
import java.util.logging.Level;
import java.util.logging.Logger;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.geometry.Pos;
import javafx.scene.Node;
import javafx.scene.control.ScrollPane;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.input.MouseEvent;
import javafx.scene.layout.AnchorPane;
import javafx.scene.layout.GridPane;
import javafx.scene.layout.VBox;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.scene.text.Text;
import javafx.scene.text.TextAlignment;
import static org.apache.poi.hssf.usermodel.HeaderFooter.file;
import org.controlsfx.control.Rating;
import entity.Evenement;
import entity.SendMail;
import static entity.SendMail.sendMail;
import static GUI.EventInterfaceController.projectPath;
import Services_event.ServiceEvent;
import java.io.IOException;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

/**
 * FXML Controller class
 *
 * @author msi
 */
public class EventFrontInterfaceController implements Initializable {

    Evenement selectedEvent;
    ServiceEvent se = new ServiceEvent();
    @FXML
    private GridPane gridEvent;
    @FXML
    private ScrollPane scroll_event;
    @FXML
    private ImageView imageQr;
    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
                        gridEvent.getChildren().clear();

        try {
            // TODO

            Affichage();
        } catch (FileNotFoundException ex) {
            Logger.getLogger(EventFrontInterfaceController.class.getName()).log(Level.SEVERE, null, ex);
        } catch (SQLException ex) {
            Logger.getLogger(EventFrontInterfaceController.class.getName()).log(Level.SEVERE, null, ex);
        }
   
       
    }    
     
    public void Affichage() throws FileNotFoundException, SQLException
    {
        
            List<Evenement> events = se.readAll();
            int row = 0;
            int column = 0;
            for (int i = 0; i < events.size(); i++) {

                //chargement dynamique d'une interface
                FXMLLoader loader = new FXMLLoader(getClass().getResource("GUI/EventFrontInterface.fxml"));
                AnchorPane pane = new AnchorPane();
                
              
                Evenement eventt = events.get(i);
                String imagePath = eventt.getImage();
              
                FileInputStream input = new FileInputStream("C:\\xampp\\htdocs\\imagesEvenet\\"+imagePath);
                Image image = new Image(input);
                ImageView imageView = new ImageView(image);
                imageView.setFitHeight(200);
                imageView.setFitWidth(200);
                
                Text EventTitre = new Text(eventt.getTitre());
                EventTitre.setFont(Font.font("Arial", FontWeight.BOLD, 14));
                EventTitre.setWrappingWidth(200);
                EventTitre.setTextAlignment(TextAlignment.CENTER);
                
                Text EventDescrip = new Text(eventt.getDescription());
                EventDescrip.setFont(Font.font("Arial", FontWeight.BOLD, 14));
                EventDescrip.setWrappingWidth(200);
                EventDescrip.setTextAlignment(TextAlignment.CENTER);

                                
                Text EventCategory = new Text(eventt.getCategorieNom());
                EventCategory.setFont(Font.font("Arial", FontWeight.BOLD, 14));
                EventCategory.setWrappingWidth(200);
                EventCategory.setTextAlignment(TextAlignment.CENTER);

                                
                Text EventDate = new Text(eventt.getDateev().toString());
                EventDate.setFont(Font.font("Arial", FontWeight.BOLD, 14));
                EventDate.setWrappingWidth(200);
                EventDate.setTextAlignment(TextAlignment.CENTER);

                
                VBox vBox = new VBox(10);
                vBox.getChildren().addAll(EventTitre,imageView, EventDescrip,EventCategory,EventDate);
                vBox.setAlignment(Pos.CENTER);
                
                gridEvent.setConstraints(vBox,column, row);
                gridEvent.getChildren().addAll(vBox);
                    
                    pane.getChildren().add(gridEvent);
                    
                    vBox.setOnMouseClicked(event -> {
            // Set selected blog as a property of the VBox element
            selectedEvent = eventt;
            // TODO: Add code to handle the selected blog
                });
                
                column++;
                if (column > 1) {
                    column = 0;
                    row++;
                }
            }
    

   
    
              }
        public static String projectPath = System.getProperty("user.dir").replace("\\", "/");

    @FXML
    private void load(MouseEvent event) {

                File f = new File(projectPath + "\\src\\qr\\" + selectedEvent.getTitre().toString()+ ".jpg");
                Image image = new Image(f.toURI().toString());

                imageQr.setImage(image);               

    }

    @FXML
    private void Participer(ActionEvent event) {
   
        sendMail("yasmine.mimouni@esprit.tn", "Evenement", "Vous avez participer a levenement :"+selectedEvent.getTitre()+"\n Description : "+selectedEvent.getDescription()+"\n Date : "+selectedEvent.getDateev());
    }
    @FXML
private void retour(ActionEvent event) throws IOException {
    Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
   
    Parent root = FXMLLoader.load(getClass().getResource("/GUI/home.fxml"));
    Scene scene = new Scene(root);
    stage.setScene(scene);
    stage.show();
}
    
}
