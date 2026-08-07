/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui;

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
import com.codename1.ui.util.Resources;
import com.codename1.util.Base64;
import com.mycompany.myapp.entities.User;
import com.mycompany.myapp.entities.services.ServiceUser;
import java.io.ByteArrayOutputStream;
import java.io.IOException;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.lang.reflect.Array;
import java.util.ArrayList;


/**
 *
 * @author bhk
 */
public class Login extends Form{
    
    Form current;
    Resources res;

    public Login() {
                        
        current=this;
        setTitle("Home");
        setLayout(BoxLayout.y());
                
        TextField tfnom= new TextField();
        tfnom.setHint(" Email");
        TextField tfpw= new TextField();
        tfpw.setHint("Password");
        tfpw.setConstraint(TextField.PASSWORD);
        Button btnValider = new Button("Login");
        Button btnInscrit = new Button("Inscription");
        Button btnMdpOub = new Button("Mot de passe oublier");
        btnInscrit.addActionListener(e-> new Inscription(current).show());
        btnMdpOub.addActionListener(e-> new ForgetPwd(current).show());

        btnValider.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent evt) {
                if (tfnom.getText().equals("")||(tfpw.getText().equals("")))
                    Dialog.show("Alert", "Please fill all the fields", new Command("OK"));
                else
                {

                    try {

                                ArrayList <User> users = new ArrayList();
                                ServiceUser sl =new ServiceUser();
                                users=sl.Login(tfnom.getText(),tfpw.getText());
                                User tmp = null;                         
                                for (User fi : users) {
                                    System.out.println("salut user " +fi);
                                    tmp = fi;
                                }
                                if(tmp == null)
                                {
                                Dialog.show("ERROR", "User or password is incorrect", new Command("OK"));
                                }
                                else
                                {
                                 new MenuPrincipal(tmp,res).show();
                                }

                    } catch (NumberFormatException e) {
                        Dialog.show("ERROR", "Status must be a number", new Command("OK"));
                    }
                    
                }
                
                
            }
        });
        
        addAll(tfnom,tfpw,btnValider,btnInscrit,btnMdpOub);
        
                
    }
     

}
