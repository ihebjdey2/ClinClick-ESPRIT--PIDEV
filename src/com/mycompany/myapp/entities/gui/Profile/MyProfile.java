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
public class MyProfile extends Form{

        
    Form current;
    
    public MyProfile(Form previous,User u) {
        current=this;

        setTitle("Mon profile");
        setLayout(BoxLayout.y());
               
        Label l2 = new Label("Nom d'utilisateur : "+u.getNom());
        Label l3 = new Label("Email : "+u.getEmail());

        Button btnValider = new Button("Editer");
                
        btnValider.addActionListener(e-> new EditProfile(current,u).show());

        
        addAll(l2,l3,btnValider);
        //Tool Bar
        getToolbar().addCommandToSideMenu("Home", null, e -> new MenuPrincipal(u).show());
        getToolbar().addCommandToSideMenu("Mon Profile", null, e -> new MyProfile(current,u).show());
        getToolbar().addCommandToSideMenu("Deconnexion", null, e -> new Login().show());
                
    }
     
}
