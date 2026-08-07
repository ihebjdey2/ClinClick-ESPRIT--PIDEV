/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.gui;

import com.codename1.ui.Form;
import com.codename1.components.ImageViewer;
import com.codename1.components.InteractionDialog;
import com.codename1.components.SpanLabel;
import com.codename1.components.ToastBar;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.plaf.Border;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.mycompany.myapp.MyApplication;

import com.mycompany.myapp.utils.Statics;
import com.mycompany.myapp.entities.gui.ReclamationManage;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import com.mycompany.myapp.entities.Reclamation;
import com.mycompany.myapp.entities.services.ReclamationService;
/**
 *
 * @author tlich
 */
public class ListeReclamation extends Form{
     public static ListeReclamation instance;
    public static Reclamation currentReclamation = null;
    Resources theme = UIManager.initFirstTheme("/theme");
    Button addBtn;
    Button searchBtn;
    Button pdfBtn;
    
    
   Label  NomLabel;
    Label  EmailLabel;
  
    Label  DescriptionLabel;
    Label  EtatLabel;

    
    SpanLabel NomSpanLabel;
    SpanLabel EmailSpanLabel;
    SpanLabel DescriptionSpanLabel;
    SpanLabel EtatSpanLabel;

    
    Label userLabel;
    ImageViewer userImage;
    ImageViewer imageIV;
    Button editBtn, deleteBtn;
    Container btnsContainer;
     Form previous;

    public ListeReclamation(Form previous) {
        super("Reclamations", new BoxLayout(BoxLayout.Y_AXIS));
        instance = this;
        this.previous = previous;
        addGUIs();
        addActions();

      //  super.getToolbar().hideToolbar();
        getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e -> previous.showBack());
    }

    

    public void refresh() {
        this.removeAll();
        addGUIs();
        addActions();
        this.refreshTheme();
    }

  private void addGUIs() {
    


          Toolbar.setGlobalToolbar(true);
        
   
        Container ReclamationModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
ReclamationModel.setUIID("containerRounded");
ReclamationModel.getAllStyles().setFgColor(0x2196F3);
 addBtn = new Button("Ajouter");


    addBtn.getAllStyles().setBgColor(0x2196F3);
addBtn.getAllStyles().setFgColor(0x2196F3);
addBtn.getAllStyles().setBorder(Border.createLineBorder(2, 0x2196F3));
    Button searchBtn = new Button("Recherche");
    
     pdfBtn = new Button("pdf");
    pdfBtn.addActionListener((evt) -> {ReclamationService.getInstance().pdf();
    // Create a new PDF document
    
});
    
      searchBtn.getAllStyles().setBgColor(0x2196F3);
searchBtn.getAllStyles().setFgColor(0x2196F3);
searchBtn.getAllStyles().setBorder(Border.createLineBorder(2, 0x2196F3));

    // Handle the "Recherche" button
   searchBtn.addActionListener((evt) -> {
    // Show an input dialog for the user to enter the event ID
    Dialog.show("Recherche", "Entrez l'ID de l'événement:", "Ok", null);
    TextField input = new TextField();
    Dialog dlg = new Dialog();
    dlg.setLayout(new BorderLayout());
    dlg.addComponent(BorderLayout.CENTER, input);
    Button searchBtn2 = new Button("Recherche");
    searchBtn2.addActionListener((evt2) -> {
        int id = Integer.parseInt(input.getText());
        Reclamation reclamation = ReclamationService.getInstance().findRec(id);
        if (reclamation != null) {
            // Clear the GUI
            this.removeAll();

            // Add the searched Evenement to the GUI
            this.add(makeReclamationModel(reclamation));
        } else {
            Dialog.show("Error", "Event not found", "OK", null);
        }
        dlg.dispose();
    });

    dlg.addComponent(BorderLayout.SOUTH, searchBtn2);
    dlg.show();
});

    // Add the "Ajouter" button and the "Recherche" button to the GUI
    this.add(addBtn);
    this.add(searchBtn);
     this.add(pdfBtn);

    // Add existing events to the GUI
    ArrayList<Reclamation> listReclamations = ReclamationService.getInstance().getAll();
    if (!listReclamations.isEmpty()) {
        for (Reclamation Reclamation : listReclamations) {
            this.add(makeReclamationModel(Reclamation));
        }
    } else {
        this.add(new Label("Aucune donnée"));
    }
}


    private void addActions() {
        addBtn.addActionListener(action -> {
            currentReclamation = null;
            new com.mycompany.myapp.entities.gui.ReclamationManage(this).show();
        });
    }

    private Component makeReclamationModel(Reclamation Reclamation) {
        
        
        Container ReclamationModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        ReclamationModel.setUIID("containerRounded");

       // User user = UserService.getInstance().getUserById(post.getUserId());
//User user = new User();
       // userLabel = new Label(user.getUsername());
//        userImage = new ImageViewer(theme.getImage("person.jpg").fill(150, 150));


        NomLabel = new Label("Nom : ");
        NomLabel.setUIID("labelDefault");
        NomSpanLabel = new SpanLabel(Reclamation.getNom());
        NomSpanLabel.setUIID("labelDefault");
        
        EmailLabel = new Label("email : ");
        EmailLabel.setUIID("labelDefault");
        EmailSpanLabel = new SpanLabel(Reclamation.getEmail());
        EmailSpanLabel.setUIID("labelDefault");
        
     
        
        DescriptionLabel = new Label("Prix : ");
        DescriptionLabel.setUIID("labelDefault");
        DescriptionSpanLabel = new SpanLabel(String.valueOf(Reclamation.getId()));
        DescriptionSpanLabel.setUIID("labelDefault");
        
        EtatLabel = new Label("Categorie : ");
        EtatLabel.setUIID("labelDefault");
        if (Reclamation.isEtat()==1)
        EtatSpanLabel = new SpanLabel("Verifié");
        else EtatSpanLabel = new SpanLabel("Non Verifié");
        EtatSpanLabel.setUIID("labelDefault");
        
     

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");

        editBtn = new Button("Modifier");
        editBtn.addActionListener(action -> {
            currentReclamation = Reclamation;
            new com.mycompany.myapp.entities.gui.ReclamationManage(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce post ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                boolean responseCode = ReclamationService.getInstance().deleteReclamation(Reclamation.getId());

                if (responseCode) {
                    currentReclamation = null;
                    dlg.dispose();
                    ReclamationModel.remove();
                    this.refresh();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du post. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);

        Container userContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
        userContainer.setUIID("containerUser");
//        userContainer.addAll(userImage, userLabel);

        ReclamationModel.addAll(
                NomLabel,
                NomSpanLabel,
                
                EmailLabel,
                EmailSpanLabel,
    
             
    
                DescriptionLabel,
                DescriptionSpanLabel,
    
                EtatLabel,
                EtatSpanLabel,
    
            
             
                btnsContainer
        );

        


      return ReclamationModel;  
    }

    
    
}
