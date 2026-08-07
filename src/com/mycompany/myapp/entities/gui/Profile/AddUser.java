/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui.Profile;

import com.codename1.capture.Capture;
import com.codename1.components.FloatingActionButton;
import static com.codename1.components.FloatingActionButton.createFAB;
import com.codename1.datatransfer.DropTarget;
import com.codename1.ext.filechooser.FileChooser;
import com.codename1.io.ConnectionRequest;
import com.codename1.io.FileSystemStorage;
import com.codename1.io.NetworkEvent;
import com.codename1.io.NetworkManager;
import com.codename1.l10n.DateFormatPatterns;
import com.codename1.messaging.Message;
import com.codename1.notifications.LocalNotification;
import com.codename1.ui.Button;
import com.codename1.ui.ButtonGroup;
import com.codename1.ui.ComboBox;
import com.codename1.ui.Command;
import com.codename1.ui.Container;
import com.codename1.ui.Dialog;
import com.codename1.ui.Display;
import com.codename1.ui.Font;
import com.codename1.ui.FontImage;
import com.codename1.ui.Form;
import com.codename1.ui.Image;
import com.codename1.ui.Label;
import com.codename1.ui.RadioButton;
import com.codename1.ui.Slider;
import com.codename1.ui.TextComponent;
import com.codename1.ui.TextField;
import com.codename1.ui.events.ActionEvent;
import com.codename1.ui.events.ActionListener;
import com.codename1.ui.geom.Dimension;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.layouts.FlowLayout;
import com.codename1.ui.plaf.Border;
import com.codename1.ui.plaf.RoundBorder;
import com.codename1.ui.plaf.Style;
import com.codename1.ui.spinner.Picker;
import com.codename1.ui.util.ImageIO;
import com.codename1.util.Base64;
import com.mycompany.myapp.entities.User;
import com.mycompany.myapp.entities.gui.Login;
import com.mycompany.myapp.entities.gui.MenuPrincipal;
import com.mycompany.myapp.entities.services.ServiceUser;
import java.io.ByteArrayOutputStream;
import java.io.IOException;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.util.ArrayList;


/**
 *
 * @author bhk
 */
public class AddUser extends Form{
    
   String filePath;
   Form detailsForm;
   String Imagecode;

   public AddUser(Form previous,User u) {
        setTitle("Ajouter Utilisateur");

        TextComponent tfemail= new TextComponent().label("email");        
        TextComponent tfnom= new TextComponent().label("nom");
        TextField tfpw= new TextField();
        tfpw.setHint("Password");
        tfpw.setConstraint(TextField.PASSWORD);
        TextField tfpwc= new TextField();
        tfpwc.setHint("Password Repeat");
        tfpwc.setConstraint(TextField.PASSWORD);

        Button btnValider = new Button("Ajouter Utilisateur");
        
        btnValider.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent evt) {
                if (tfnom.getText().equals("")||(tfemail.getText().equals("")||(tfpw.getText().equals(""))||(tfpwc.getText().equals(""))))
                {
                    Dialog.show("Alert", "Please fill all the fields", new Command("OK"));
                }
                else if(!tfpw.getText().equals(tfpwc.getText()))
                {
                        Dialog.show("Alert", "Password don't match", new Command("OK"));
                }
                else
                {
                    try {
                            User u = new User(tfemail.getText(),tfnom.getText(),tfpw.getText());
                           if( ServiceUser.getInstance().addUser(u)){
                                   Dialog.show("Success","Utilisateur ajouter avec success",new Command("OK"));
                                   new MenuPrincipal(u).show();
                        }
                        else
                            Dialog.show("ERROR", "Server error", new Command("OK"));
                    } catch (NumberFormatException e) {
                        Dialog.show("ERROR", "Status must be a number", new Command("OK"));
                    }
                    
                }
            }
        });
        addAll(tfemail,tfnom,tfpw,tfpwc,btnValider);
        getToolbar().addMaterialCommandToLeftBar("", FontImage.MATERIAL_ARROW_BACK, e-> previous.showBack());
    }
 /*                 
    public void sendMail(String Email) {
        ConnectionRequest req = new ConnectionRequest();
        req.setUrl("http://localhost/Email/sendmail.php?email="+ Email);

        req.addResponseListener(new ActionListener<NetworkEvent>() {

            @Override
            public void actionPerformed(NetworkEvent evt) {

                byte[] data = (byte[]) evt.getMetaData();
                String s = new String(data);
                System.err.println("Mail Sent");
            }
        });

        NetworkManager.getInstance().addToQueue(req);
    }
*/
}

