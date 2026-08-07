/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.myapp.entities;

/**
 *
 * @author mohta
 */
public class Categorie {
    int id;
    String nom,information;

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

    public String getInformation() {
        return information;
    }

    public void setInformation(String information) {
        this.information = information;
    }

    public Categorie(int id, String nom, String information) {
        this.id = id;
        this.nom = nom;
        this.information = information;
    }

    public Categorie(String nom, String information) {
        this.nom = nom;
        this.information = information;
    }

    @Override
    public String toString() {
        return nom;
    }
    
    
    
}
