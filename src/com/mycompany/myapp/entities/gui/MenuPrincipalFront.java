/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui;


import com.mycompany.myapp.entities.gui.ListeReclamation;
import com.mycompany.myapp.entities.gui.ReclamationManage;
import com.mycompany.myapp.entities.Reclamation;
import com.mycompany.myapp.entities.Event;
import com.codename1.ui.Button;
import com.codename1.ui.Form;
import com.codename1.ui.Label;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.util.Resources;
import com.mycompany.myapp.entities.User;
import com.mycompany.myapp.entities.gui.Profile.AddUser;
import com.mycompany.myapp.entities.gui.Profile.LisUsers;
import com.mycompany.myapp.entities.gui.Profile.MyProfile;

/**
 *
 * @author sbs
 */
public class MenuPrincipalFront extends BaseForm {
  

    public MenuPrincipalFront(User u,Resources res) {
          
        Form current;
  
        current = this;
    ReclamationManage reclamationManage = new ReclamationManage(current);

        setTitle("Home");
        setLayout(BoxLayout.y());

        //BUTTONS
        add(new Label("Choisissez une option"));
        Button btnUsers = new Button("Chercher un utilisateur");
        Button btnCreate = new Button("faire une reclamation");
        Button btnEvent = new Button("listes des evenement");

        btnUsers.addActionListener(e -> new LisUsers(u).show());
        btnCreate.addActionListener(e -> new AddUser(current, u).show());

        //Tool Bar
        getToolbar().addCommandToSideMenu("Reclamation", null, e -> {
            Reclamation reclamation = reclamationManage.getCurrentReclamation();
            reclamationManage.setCurrentReclamation(null);
            new com.mycompany.myapp.entities.gui.ReclamationManage(current, reclamation).show();
        });
         
       
           
            
      

        getToolbar().addCommandToSideMenu("Mon Profile", null, e -> new MyProfile(current, u).show());
        getToolbar().addCommandToSideMenu("Log out", null, e -> new Login().show());

        addAll(btnUsers, btnCreate);
    }
}
