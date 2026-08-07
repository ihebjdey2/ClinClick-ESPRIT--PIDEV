/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.util.Date;
import java.time.LocalDate;


/**
 *
 * @author belkn
 */
public class Article{
    private int refA;
    private String nomA;
        private Categorie CategorieId;
    private int quantite ;
    private LocalDate date;

    public Article(int refA, String nomA, Categorie CatId, int quantite, LocalDate date) {
        this.refA = refA;
        this.nomA = nomA;
        this.CategorieId = CatId;
        this.quantite = quantite;
        this.date = date;
    }
    



    public Article() {
    }

    public Article(String nomA, int quantite, int CatId) {
        this.nomA = nomA;
        this.quantite = quantite;
        this.CategorieId.CatId=CatId;    }

    public Article(int refA, String nomA, int quantite, int CatId) {
   this.refA = refA;
        this.nomA = nomA;
        this.quantite = quantite;
        this.CategorieId= new Categorie (CatId);
    }

    public Article(int refA, String nomA, int quantiteA, LocalDate date, Categorie categorie) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    public Article(String nomA, int quantiteA, int CatId, LocalDate date) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    public Article(int refA, String nomA, int quantiteA, Categorie CatId) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    public Article(String redfez, int i, LocalDate now, Categorie category) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    public Article(String nomA, int quantiteA, LocalDate date, int CatId) {
        this.nomA = nomA;
        this.CategorieId=new Categorie (CatId);
        this.quantite = quantiteA;
        this.date= date;
        
    }

    public Article(int refA, String nomA, int quantiteA, LocalDate date, int CatId) {
        this.nomA = nomA;
        this.CategorieId=new Categorie (CatId);
        this.quantite = quantiteA;
        this.date= date;
        this.refA=refA;
    }

    public Article(String nomA, int quantiteA, Categorie categorie, LocalDate date) {
        this.nomA = nomA;
        this.CategorieId = categorie;
        this.quantite = quantiteA;
        this.date = date;
    }



  
    public String toString() {
        return "Article{" + "Réference article=" + refA + ", nom d'article=" + nomA +", la quantite est "+quantite+ "La date d'expiration est: "+date+", la Categorie est "+CategorieId+ '}';
    }

    public int getRef() {
        return refA;
    }
    public String getNom() {
        return nomA;
    }
    public int getQuantite(){
        return quantite;
    }
    
    public LocalDate getDate(){
        return date;
    }

    public void setRef(int refA) {
        this.refA = refA;
    }

    public void setNom(String nomA) {
        this.nomA = nomA;
    }
public void setQuantite(int quantite){
    this.quantite= quantite ;
}

 public void setDate(LocalDate date){
     this.date=date;
 }  
 
 public void getCategorieId3(int CatId) {
    this.CategorieId = new Categorie(CatId);
}

    public int getCategorieId() {
        return CategorieId.CatId;
    }
        public Categorie getCategorieId2() {
        return CategorieId;
    }

    public void setCategorieId(int CategorieId) {
        this.CategorieId.CatId = CategorieId;
    }
    public void setCategorieId2(Categorie CategorieId) {
        this.CategorieId = CategorieId;
    }
 
 
}
