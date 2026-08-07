/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.sql.Date;
import java.sql.Time;
import javafx.scene.image.ImageView;

/**
 *
 * @author msi
 */
public class Evenement {
    private int id;
        
    private Category cattegory;
    private int id_categorie;
    private String categorieNom;

    private String titre;
    private String description;
    private String image;
    private Date dateev;
    private ImageView img;
    
    public Evenement(int id, int id_categorie, String titre, String description, String image, Date dateev) {
        this.id = id;
        this.id_categorie = id_categorie;
        this.titre = titre;
        this.description = description;
        this.image = image;
        this.dateev = dateev;
    }

    public Evenement(int id_categorie, String titre, String description, String image, Date dateev) {
        this.id_categorie = id_categorie;
        this.titre = titre;
        this.description = description;
        this.image = image;
        this.dateev = dateev;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Category getCattegory() {
        return cattegory;
    }

    public void setCattegory(Category cattegory) {
        this.cattegory = cattegory;
    }

    public int getId_categorie() {
        return id_categorie;
    }

    public void setId_categorie(int id_categorie) {
        this.id_categorie = id_categorie;
    }

    public String getCategorieNom() {
        return categorieNom;
    }

    public void setCategorieNom(String categorieNom) {
        this.categorieNom = categorieNom;
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

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public Date getDateev() {
        return dateev;
    }

    public void setDateev(Date dateev) {
        this.dateev = dateev;
    }

    public ImageView getImg() {
        return img;
    }

    public void setImg(ImageView img) {
        this.img = img;
    }

    @Override
    public String toString() {
        return "Evenement{" + "id=" + id + ", cattegory=" + cattegory + ", id_categorie=" + id_categorie + ", categorieNom=" + categorieNom + ", titre=" + titre + ", description=" + description + ", image=" + image + ", dateev=" + dateev + ", img=" + img + '}';
    }

   
}
