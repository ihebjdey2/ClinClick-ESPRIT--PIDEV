/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui.Profile;

import com.codename1.components.InfiniteProgress;
import com.codename1.components.MultiButton;
import com.codename1.components.SpanLabel;
import com.codename1.ui.Button;
import com.codename1.ui.Command;
import com.codename1.ui.Component;
import com.codename1.ui.Container;
import com.codename1.ui.Dialog;
import com.codename1.ui.Display;
import com.codename1.ui.EncodedImage;
import com.codename1.ui.FontImage;
import com.codename1.ui.Form;
import com.codename1.ui.Image;
import com.codename1.ui.Toolbar;
import com.codename1.ui.URLImage;
import com.codename1.ui.events.ActionEvent;
import com.codename1.ui.events.ActionListener;
import com.codename1.ui.layouts.BoxLayout;
import com.mycompany.myapp.entities.User;
import com.mycompany.myapp.entities.gui.Login;
import com.mycompany.myapp.entities.gui.MenuPrincipal;
import com.mycompany.myapp.entities.services.ServiceUser;
import java.util.ArrayList;
import java.util.Date;

/**
 *
 * @author sbs
 */
public class LisUsers extends Form{
        Form current;

        public LisUsers(User u) {
        setTitle("Liste des Utilisateurs");
        
          Container co;
                       //search
             Toolbar.setGlobalToolbar(true);
             add(new InfiniteProgress());
             
                Display.getInstance().scheduleBackgroundTask(()-> {
                    // this will take a while...
                    Display.getInstance().callSerially(() -> {
                    removeAll();
                    ArrayList <User> users = new ArrayList();
                        ServiceUser sa =new ServiceUser();
                    users=sa.getAllUsers();
                             for (User fi : users) {
                            MultiButton m = new MultiButton();
                            m.setTextLine1("Name : "+String.valueOf(fi.getNom()));
                            m.setTextLine2("Email :"+String.valueOf(fi.getEmail()));

                            m.addLongPressListener(new ActionListener() {
                                            @Override
            public void actionPerformed(ActionEvent evt) {               
                if (Dialog.show("Confirmation", "Voulez vous Supprimer cette utilisateur ?", "Supprimer", "Annuler")) {
                        if( ServiceUser.getInstance().deleteUser(fi)){
                            {
                                   Dialog.show("Success","supprimer",new Command("OK"));
                                   new LisUsers(u).show();
                            }
                   
                }
            }    
            }
        });

                            add(m);
                             }
                     revalidate();
                    });
                });
    getToolbar().addSearchCommand(e -> {
    String text = (String)e.getSource();
    if(text == null || text.length() == 0) {
        // clear search
        for(Component cmp : getContentPane()) {
            cmp.setHidden(false);
            cmp.setVisible(true);
        }
        getContentPane().animateLayout(150);
    } else {
        text = text.toLowerCase();
        for(Component cmp : getContentPane()) {
            MultiButton mb = (MultiButton)cmp;
            String line1 = mb.getTextLine1();
            String line2 = mb.getTextLine2();
            boolean show = line1 != null && line1.toLowerCase().indexOf(text) > -1 ||
            line2 != null && line2.toLowerCase().indexOf(text) > -1;
            mb.setHidden(!show);
            mb.setVisible(show);
        }
        getContentPane().animateLayout(150);
    }
}, 4);
        //Tool Bar
        getToolbar().addCommandToSideMenu("Home", null, e -> new MenuPrincipal(u).show());
        getToolbar().addCommandToSideMenu("Mon Profile", null, e -> new MyProfile(current,u).show());
        getToolbar().addCommandToSideMenu("Deconnexion", null, e -> new Login().show());
    }

    
}
