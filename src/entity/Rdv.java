/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.util.Objects;

/**
 *
 * @author lengu
 */

public class Rdv {
    private int id;
    private String nom;
    private String date;
  //  private category_rdv category_rdv;

    public Rdv(int id, String nom, String date) {
        this.id = id;
        this.nom = nom;
        this.date = date;
        // this.category_rdv = category_rdv;
    }

    public int getId() {
        return id;
    }
    
    

    @Override
    public String toString() {
        return "Rdv{" +
                "id=" + id +
                ", nom='" + nom + '\'' +
                ", date='" + date + '\'' +
            //    ", category_rdv=" + category_rdv +
                '}';
    }
    

    public void setId(int id) {
        this.id = id;
    }

    public String getNom() {
        return nom;
    }

    @Override
    public int hashCode() {
        int hash = 5;
        hash = 61 * hash + Objects.hashCode(this.id);
        return hash;
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) {
            return true;
        }
        if (obj == null) {
            return false;
        }
        if (getClass() != obj.getClass()) {
            return false;
        }
        final Rdv other = (Rdv) obj;
        if (!Objects.equals(this.id, other.id)) {
            return false;
        }
        return true;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    
}
    

