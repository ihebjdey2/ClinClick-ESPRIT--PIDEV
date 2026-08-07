/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package Services_event;

import entity.Category;
import java.sql.SQLException;
import java.util.List;
import java.sql.*;
import java.sql.PreparedStatement;
import tools.MaConnection;
import java.util.ArrayList;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author House
 */
public class ServiceCategorie implements IService<Category> {

    private Connection con;
    private Statement ste;
    private PreparedStatement pst ;
    private ResultSet res ;

    public ServiceCategorie() {
        con = MaConnection.getInstance().getCnx();

    }

    @Override
    public void ajouter(Category t) throws SQLException {
        ste = con.createStatement();
        String requeteInsert = "INSERT INTO category ( `nom`,`libelle`  ) VALUES ('" + t.getNom()+ "','" + t.getLibelle()+ "');";
        try {
            ste=con.createStatement();
            ste.executeUpdate(requeteInsert);
            
        } catch (SQLException ex) {
            Logger.getLogger(ServiceCategorie.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
        @Override
    public void delete(Category t) throws SQLException {
        try {
            String requete = " delete from category where id='"+t.getId()+"'" ;
            pst = con.prepareStatement(requete);
              ste=con.createStatement();
            ste.executeUpdate(requete);
            
        } catch (SQLException ex) {
            Logger.getLogger(ServiceCategorie.class.getName()).log(Level.SEVERE, null, ex);
        }    
    }

    @Override
    public void update(Category t) throws SQLException {
        try {
            String requete = " update category set nom=?,libelle=?  where id='"+t.getId()+"'"  ;
            pst = con.prepareStatement(requete);
            pst.setString(1,t.getNom());
            pst.setString(2,t.getLibelle());
            pst.executeUpdate();
        } catch (SQLException ex) {
            Logger.getLogger(ServiceCategorie.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    @Override
    public List<Category> readAll() throws SQLException {
    List<Category> arr=new ArrayList<>();
    ste=con.createStatement();
    ResultSet rs=ste.executeQuery("select * from category");
     while (rs.next()) {                
               Category p=new Category(rs.getInt(1), rs.getString("nom"),rs.getString("libelle"));
     arr.add(p);
     }
    return arr;
    }
    
    @Override
    public List<Category> getTrier() throws SQLException {
    List<Category> arr=new ArrayList<>();
    ste=con.createStatement();
    ResultSet rs=ste.executeQuery("select * from category ORDER BY nom DESC");
     while (rs.next()) {                
               Category p=new Category(rs.getInt(1), rs.getString("nom"),rs.getString("libelle"));
     arr.add(p);
     }
    return arr;
    }

  public Category getByName(String n) {
          Category a = null;
         String requete = " select* from category  where nom = '"+n+"'" ;
        try {
            ste = con.createStatement();
            res=ste.executeQuery(requete);
            if (res.next())
            {a=new Category(res.getInt(1),res.getString(2),res.getString(3));}
        } catch (SQLException ex) {
            Logger.getLogger(ServiceCategorie.class.getName()).log(Level.SEVERE, null, ex);
        }
        return a ;
        
    }
    
    public Category getById(int id) {
          Category a = null;
         String requete = " select* from category  where id='"+id+"'" ;
        try {
           
            ste = con.createStatement();
            res=ste.executeQuery(requete);
            if (res.next())
            {a=new Category(res.getInt(1),res.getString(2),res.getString(3));}
        } catch (SQLException ex) {
            Logger.getLogger(ServiceCategorie.class.getName()).log(Level.SEVERE, null, ex);
        }
        return a ;
    }
    




}
