/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


package entity;

/**
 *
 * @author Asus
 */
import com.sun.faces.config.WebConfiguration;
import java.util.Date;

public class User {
    private int id;
    private String email;
    private String roles;
    private String password;
    private Boolean is_verified;
    private Date date_naissance;
    private String nom;
    private String prenom;
    private String genre;
    
    public static User Current_User;

    public User() {
    }

    public User(int id, String email, String roles, String password, Boolean is_verified, Date date_naissance, String nom, String prenom, String genre) {
        this.id = id;
        this.email = email;
        this.roles = roles;
        this.password = password;
        this.is_verified = is_verified;
        this.date_naissance = date_naissance;
        this.nom = nom;
        this.prenom = prenom;
        this.genre = genre;
    }

    public User(String email, String password) {
        this.email = email;
        this.password = password;
    }

    
    public User(String email, String roles, String password, Boolean is_verified, Date date_naissance, String nom, String prenom, String genre) {
        this.email = email;
        this.roles = roles;
        this.password = password;
        this.is_verified = is_verified;
        this.date_naissance = date_naissance;
        this.nom = nom;
        this.prenom = prenom;
        this.genre = genre;
    }
    public User(String email, String roles, String nom, String prenom, String genre) {
        this.email = email;
        this.roles = roles;
        this.nom = nom;
        this.prenom = prenom;
        this.genre = genre;
    }

    public User(String email, String roles, String password, java.sql.Date date_naissance, String nom, String prenom, String genre) {
        this.email = email;
        this.roles = roles;
        this.password = password;
        this.date_naissance = date_naissance;
        this.nom = nom;
        this.prenom = prenom;
        this.genre = genre;
        
    }

    public User(String email, String password, java.sql.Date date_naissance, String nom, String prenom, String genre) {
          this.email = email;
          this.password = password;
           this.date_naissance = date_naissance;
        this.nom = nom;
        this.prenom = prenom;
        this.genre = genre;
    }

    public User(String mail, String role, String mdp, WebConfiguration.DisableUnicodeEscaping disableUnicodeEscaping, java.sql.Date date, String nom, String prenom, String genre) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

   

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getRoles() {
        return roles;
    }

    public void setRoles(String roles) {
        this.roles = roles;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public Boolean getIs_verified() {
        return is_verified;
    }

    public void setIs_verified(Boolean is_verified) {
        this.is_verified = is_verified;
    }

    public Date getDate_naissance() {
        return date_naissance;
    }

    public void setDate_naissance(Date date_naissance) {
        this.date_naissance = date_naissance;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public String getPrenom() {
        return prenom;
    }

    public void setPrenom(String prenom) {
        this.prenom = prenom;
    }

    public String getGenre() {
        return genre;
    }

    public void setGenre(String genre) {
        this.genre = genre;
    }

    public static void setCurrent_User(User Current_User) {
        User.Current_User = Current_User;
    }

    public static User getCurrent_User() {
        return Current_User;
    }

    @Override
    public String toString() {
        return "User{" + "id=" + id + ", email=" + email + ", roles=" + roles + ", password=" + password + ", is_verified=" + is_verified + ", date_naissance=" + date_naissance + ", nom=" + nom + ", prenom=" + prenom + ", genre=" + genre + '}';
    }

    
 
    
    
}
