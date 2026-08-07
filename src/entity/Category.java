/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;
/**
 *
 * @author msi
 */
public class Category {
    private int id;
    private String nom;
    private String libelle;

    public Category(int id, String nom, String libelle) {
        this.id = id;
        this.nom = nom;
        this.libelle = libelle;
    }

    public Category(String nom, String libelle) {
        this.nom = nom;
        this.libelle = libelle;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public String getLibelle() {
        return libelle;
    }

    public void setLibelle(String libelle) {
        this.libelle = libelle;
    }

    @Override
    public String toString() {
        return "Cattegory{" + "id=" + id + ", nom=" + nom + ", libelle=" + libelle + '}';
    }



    
    
}
