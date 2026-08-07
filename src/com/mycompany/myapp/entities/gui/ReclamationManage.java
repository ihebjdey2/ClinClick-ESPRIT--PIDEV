/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui;

import com.codename1.ui.Form;
import com.codename1.capture.Capture;
import com.codename1.components.ImageViewer;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.list.DefaultListModel;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.mycompany.myapp.MyApplication;
import com.mycompany.myapp.entities.Reclamation;
import com.mycompany.myapp.entities.services.ReclamationService;
import com.mycompany.myapp.utils.Statics;

import java.io.IOException;
import static java.lang.Integer.parseInt;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

/**
 *
 * @author tlich
 */
public class ReclamationManage extends Form{
     Resources theme = UIManager.initFirstTheme("/theme");
    boolean imageEdited = false;

    Reclamation currentReclamation;

    Label  NomLabel;
    Label  EmailLabel;
    
    Label  DescriptionLabel;
    Label  EtatLabel;
   
    
    TextArea NomTF;
    TextArea EmailTF;
  
    TextArea DescriptionTF;
    TextArea EtatTF;
   
    
    
    Button  manageButton;

    Form previous;

    public ReclamationManage(Form previous) {
        super(ListeReclamation.currentReclamation == null ? "Ajouter une reclamation" : "Modifier la reclamation", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;
        currentReclamation = ListeReclamation.currentReclamation;

        addGUIs();
        addActions();

        getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e -> previous.showBack());
    }
  public void setCurrentReclamation(Reclamation reclamation) {
        currentReclamation = reclamation;
    }
  public ReclamationManage(Form previous, Reclamation currentReclamation) {
        this.previous = previous;
        this.currentReclamation = currentReclamation;

        // rest of the constructor code...
    }
      public  Reclamation getCurrentReclamation() {
        return currentReclamation;
    }
    private void addGUIs() {

        

        NomLabel = new Label("Nom : ");
        NomLabel.setUIID("labelDefault");
        NomTF = new TextArea();
        NomTF.setHint("Tapez le nom du Reclamation");
        
        EmailLabel = new Label("Email : ");
        EmailLabel.setUIID("labelDefault");
        EmailTF = new TextArea();
        EmailTF.setHint("Tapez l'email du Reclamation");
        
      
        
        DescriptionLabel = new Label("Description : ");
        DescriptionLabel.setUIID("labelDefault");
        DescriptionTF = new TextArea();
        DescriptionTF.setHint("Tapez la description du Reclamation");
        
        
       
   

        if (currentReclamation == null) {
            manageButton = new Button("Ajouter");
        } else {
            NomTF.setText(currentReclamation.getNom());
            EmailTF.setText(currentReclamation.getEmail());
            DescriptionTF.setText(currentReclamation.getDescription());
            


            manageButton = new Button("Modifier");
    }

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(
                NomLabel, NomTF,
                EmailLabel, EmailTF,
              
                DescriptionLabel, DescriptionTF,
                manageButton
        );

        this.addAll(container);
    }

    private void addActions() {
        
        if (currentReclamation == null) {
            manageButton.addActionListener(action -> {
                if (controleDeSaisie()) {
                    int responseCode;
                   
                        responseCode = ReclamationService.getInstance().ajoutReclamation(
                                new Reclamation(
                                        NomTF.getText(),
                                        EmailTF.getText(),
                                       
                                        DescriptionTF.getText()
                                        
                                        
                                )
                        );
                    if (responseCode == 200) {
                        Dialog.show("Succés", "Reclamation ajouté avec succes", new Command("Ok"));
                    } else {
                        Dialog.show("Erreur", "Erreur d'ajout de Reclamation. Code d'erreur : " + responseCode, new Command("Ok"));
                    }

                    showBackAndRefresh();
                }
            });
        } else {
            manageButton.addActionListener(action -> {
                if (controleDeSaisie()) {
                    int responseCode;
                    
                        responseCode = ReclamationService.getInstance().modifierReclamation(
                                new Reclamation(
                                        currentReclamation.getId(),
                                        NomTF.getText(),
                                        EmailTF.getText(),
                                      
                                        DescriptionTF.getText()
                                        
                                )
                        );
                    if (responseCode == 200) {
                        Dialog.show("Succés", "Reclamation modifié avec succes", new Command("Ok"));
                    } else {
                        Dialog.show("Erreur", "Erreur de modification de Reclamation. Code d'erreur : " + responseCode, new Command("Ok"));
                    }

                    showBackAndRefresh();
                }
            });
        }
    }

    private void showBackAndRefresh() {
        ListeReclamation.instance.refresh();
        previous.showBack();
    }

     private boolean controleDeSaisie() {

        if (NomTF.getText().equals("")) {
            Dialog.show("Avertissement", "Veuillez saisir le nom", new Command("Ok"));
            return false;
        }

        return true;
    }
    
}
