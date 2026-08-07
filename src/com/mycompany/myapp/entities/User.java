/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities;

import java.lang.reflect.Array;

/**
 *
 * @author SBS
 */
public class User {
    private int id;
    private String email;
    private String nom;
    private String password;
    private String confirm_password;
  

        
        
        
    public User(int id, String nom) {
        this.id = id;
        this.nom = nom;
    }

    public User(int id, String email, String nom, String password) {
        this.id = id;
        this.email = email;
        this.nom = nom;
        this.password = password;
    }

    public User(String email, String nom, String password) {
        this.email = email;
        this.nom = nom;
        this.password = password;
    }
    

    public User() {
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getConfirm_password() {
        return confirm_password;
    }

    public void setConfirm_password(String confirm_password) {
        this.confirm_password = confirm_password;
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

    @Override
    public String toString() {
        return "User{" + "id=" + id + ", email=" + email + ", nom=" + nom + ", password=" + password + ", confirm_password=" + confirm_password + '}';
    }
    
}
