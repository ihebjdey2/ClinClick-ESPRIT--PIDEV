/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities;

import java.util.Date;

/**
 *
 * @author Lenovo
 */
public class Event {
    //nemchio taw nchofo entity fi symfony
    
    private int id;
    private String titre,description;
    private String date;
    private String categorie;

    public Event() {
    }

    
    
    
    public Event(int id, String titre, String description, String date, String categorie) {
        this.id = id;
        this.titre = titre;
        this.description = description;
        this.date = date;
        this.categorie = categorie;
    }

    public Event(String titre, String description, String date, String categorie) {
        this.titre = titre;
        this.description = description;
        this.date = date;
        this.categorie = categorie;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getTitre() {
        return titre;
    }

    public void setTitre(String titre) {
        this.titre = titre;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getCategorie() {
        return categorie;
    }

    public void setCategorie(String categorie) {
        this.categorie = categorie;
    }

    }
