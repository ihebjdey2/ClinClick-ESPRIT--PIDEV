/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui;

import com.codename1.ui.Button;
import com.mycompany.myapp.entities.gui.ListeReclamation;
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
public class MenuPrincipal extends Form{
    Form current;

    public MenuPrincipal(User u,Resources res) {
                current=this;
        setTitle("Home");
        setLayout(BoxLayout.y());

        //BUTTONS
        add(new Label("Choisissez une option"));
        Button btnUsers = new Button("Chercher un utilisateur");
        Button btnCreate = new Button("Creer un utilisateur");
         Button btnListReclamation = new Button("liste des reclamation");
          Button btnEvent = new Button("liste des evenement");
        btnUsers.addActionListener(e-> new LisUsers(u).show());
        btnCreate.addActionListener(e-> new AddUser(current,u).show());
           btnListReclamation.addActionListener(e-> new ListeReclamation(current).show());
                 btnEvent.addActionListener(e -> new ListEventForm(res).show());
     
        //Tool Bar
        getToolbar().addCommandToSideMenu("Home", null, e -> new MenuPrincipal(u,res).show());
        getToolbar().addCommandToSideMenu("Mon Profile", null, e -> new MyProfile(current,u).show());
        getToolbar().addCommandToSideMenu("Log out", null, e -> new Login().show());

        addAll(btnUsers,btnCreate,btnListReclamation,btnEvent);

    }

}

