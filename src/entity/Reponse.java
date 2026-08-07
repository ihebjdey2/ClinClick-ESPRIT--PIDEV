/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

/**
 *
 * @author Skymil
 */
import java.util.Date;

public class Reponse {
    private int id;
    private int relation_reclamation_id;
    private String objet;
    private String message;
    private Date date_reponse;

    public Reponse() {
    }

    public Reponse(int reclamation_id, String objet, String description, Date date_reponse) {
        this.relation_reclamation_id = reclamation_id;
        this.objet = objet;
        this.message = description;
        this.date_reponse = date_reponse;
    }

    public Reponse(int id, int reclamation_id, String objet, String description, Date date_reponse) {
        this.id = id;
        this.relation_reclamation_id = reclamation_id;
        this.objet = objet;
        this.message = description;
        this.date_reponse = date_reponse;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getRelation_Reclamation_id() {
        return relation_reclamation_id;
    }

    public void setRelation_Reclamation_id(int reclamation_id) {
        this.relation_reclamation_id = reclamation_id;
    }

    public String getObjet() {
        return objet;
    }

    public void setObjet(String objet) {
        this.objet = objet;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String description) {
        this.message = description;
    }

    

    @Override
    public String toString() {
        return "Reponse{" + "id=" + id + ", reclamation_id=" + relation_reclamation_id + ", objet=" + objet + ", message=" + message +  '}'+"\n";
    }
    
}
