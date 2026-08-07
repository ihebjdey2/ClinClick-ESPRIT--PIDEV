/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities;


import java.util.Date;


/**
 *
 * @author tlich
 */

public class Reclamation {
    private int id;
    private String Nom,Email,Description;
    private int Etat;
    

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNom() {
        return Nom;
    }

    public void setNom(String Nom) {
        this.Nom = Nom;
    }

    public String getEmail() {
        return Email;
    }

    public void setEmail(String Email) {
        this.Email = Email;
    }

   

    public String getDescription() {
        return Description;
    }

    public void setDescription(String Description) {
        this.Description = Description;
    }

    public int isEtat() {
        return Etat;
    }

    public void setEtat(int Etat) {
        this.Etat = Etat;
    }

   

    public Reclamation(int id, String Nom, String Email, String Description, int Etat) {
        this.id = id;
        this.Nom = Nom;
        this.Email = Email;
     
        this.Description = Description;
        this.Etat = Etat;
       
    }

    public Reclamation(String Nom, String Email, String Description) {
        this.Nom = Nom;
        this.Email = Email;
    
        this.Description = Description;
        this.Etat = 0;
     
    }

    public Reclamation(int id ,String Nom, String Email , String Description) {
        this.Nom = Nom;
        this.id = id;
        this.Email = Email;
       
        this.Description = Description;
        this.Etat = 0;
    }
    

    public Reclamation() {
    }
    
    
}
