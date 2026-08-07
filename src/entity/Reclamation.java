/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.util.Date;

public class Reclamation {


     private int id;
    private String nom;
     private String email;
      private String description;
    private boolean etat;
    private int rating;
  
    public Reclamation() {
    }

    public Reclamation(String email, String objet, String description,   Boolean etat) {
        this.email = email;
        this.nom = objet;
        this.description = description;

    
        this.etat = etat;
    }

    public Reclamation(int id, String email, String objet, String description,  Boolean etat) {
        this.id = id;
        this.email = email;
        this.nom = objet;
        this.description = description;
    
   
        this.etat = etat;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }
       public int getRating() {
        return rating;
    }

    public void setRating(int id) {
        this.rating = id;
    }


    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }
     public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }


    /**
     * @return the depot
     */
    public boolean getEtat() {
        return etat;
    }

    /**
     * @param depot the depot to set
     */
    public void setEtat(boolean etat) {
        this.etat = etat;
    }
    


    @Override
    public String toString() {
        return "Reclamation{" + "id=" + id + ", email=" + email + ", nom=" + nom + ", description=" + description   +  ", etat=" + etat + '}'+"\n";
    }
    
    
}
