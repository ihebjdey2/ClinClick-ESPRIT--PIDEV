/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package services;
import java.sql.*;
import java.util.*;
import tools.MaConnection;
import java.sql.Connection;
import entity.category_rdv;
import entity.Rdv;

/**
 *
 * @author lengu
 */
public class Category_rdvService implements Interface{
    Statement ste;
    Connection cnx = MaConnection.getInstance().getCnx();
    
    public void ajouterCat(category_rdv c) {
        // TODO Auto-generated method stub
        try {
            ste = cnx.createStatement();
            String req = "Insert into  values(0,'"
                    + c.getNom() + "')";
            ste.executeUpdate(req);
            System.out.println("categorie ajouté");
        } catch (SQLException ex) {
            System.out.println("Echec !!!!");
        }
    }
    
    public void modifierCat(category_rdv c) {
        // TODO Auto-generated method stub
        try {
            String req = "UPDATE `category_rdv` SET "
                    + "`nom` = '" + c.getNom() + "', "
                    + "WHERE `category_rdv`.`id` = " + c.getId();
            Statement st = cnx.createStatement();
            st.executeUpdate(req);
            System.out.println("category_rdv  updated !");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
    }
    
    public List<category_rdv> chercherCat(String nom) {
    List<category_rdv> categories = new ArrayList<>();
    String req = "SELECT * FROM rdv WHERE nom LIKE '%" + nom + "%'";
    try {
        Statement ste = cnx.createStatement();
        ResultSet result = ste.executeQuery(req);
        while (result.next()) {
            category_rdv category_rdv = new category_rdv(result.getInt("id"), result.getString("nom"));
            categories.add(category_rdv);
        }
    } catch (SQLException ex) {
        System.out.println(ex);
    }
    return categories;
}
    
    public void supprimerCat(int id) {
        // TODO Auto-generated method stub
        try {
            String req = "DELETE FROM `category_rdv` WHERE id = " + id;
            Statement st = cnx.createStatement();
            st.executeUpdate(req);
            System.out.println("category_rdv deleted !");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
    }
    
    public List<category_rdv> affichercat() {
        // TODO Auto-generated method stub

        List<category_rdv> pers = new ArrayList<category_rdv>();
        try {
            String req = "SELECT * FROM `category_rdv`";
            Statement ste = cnx.createStatement();
            ResultSet result = ste.executeQuery(req);

            while (result.next()) {
                category_rdv resultcategory_rdv;
                resultcategory_rdv = new category_rdv(
                        result.getInt("id"), 
                        result.getString("nom"));
                pers.add(resultcategory_rdv);
            }
            System.out.println(pers);

        } catch (SQLException ex) {
            System.out.println(ex);
        }
        return pers;
    }

    @Override
    public void ajouterRdv(Rdv r) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    @Override
    public void modifierRdv(Rdv r) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    @Override
    public void supprimerRdv(int id) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    @Override
    public List<Rdv> afficherrdv() {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    public void modifiercategory_rdv(category_rdv c) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }
    
}
