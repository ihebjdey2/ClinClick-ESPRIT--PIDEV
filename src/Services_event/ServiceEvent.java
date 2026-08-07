/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package Services_event;

import entity.Evenement;
import java.sql.SQLException;
import java.util.List;
import java.sql.*;
import java.sql.PreparedStatement;
import tools.MaConnection;
import java.util.ArrayList;
import java.util.logging.Level;
import java.util.logging.Logger;
import entity.Category;

/**
 *
 * @author msi
 */
public class ServiceEvent implements IService<Evenement> {

    private Connection con;
    private Statement ste;
    private PreparedStatement pst ;
    private ResultSet res ;

    public ServiceEvent() {
            con=MaConnection.getInstance().getCnx();

    }

    @Override
    public void ajouter(Evenement a) throws SQLException {
        try {
        PreparedStatement PS = con.prepareStatement("INSERT INTO `evenement` (`category_id`, `titre`, `description`, `image`, `date`) VALUES (?, ?, ?, ?, ?  );");
        PS.setInt(1, a.getId_categorie());
        PS.setString(2, a.getTitre());
        PS.setString(3, a.getDescription());
        PS.setString(4, a.getImage());
        PS.setDate(5, a.getDateev());
        PS.executeUpdate();
        
        } catch (SQLException ex) {
            Logger.getLogger(ServiceEvent.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
        @Override
    public void delete(Evenement t) throws SQLException {
        try {
            String requete = " delete from evenement where id='"+t.getId()+"'" ;
            pst = con.prepareStatement(requete);
              ste=con.createStatement();
            ste.executeUpdate(requete);
        } catch (SQLException ex) {
            Logger.getLogger(ServiceEvent.class.getName()).log(Level.SEVERE, null, ex);
        }   
    }

    @Override
    public void update(Evenement t) throws SQLException {
        try {
            String requete = " update evenement set category_id=? , titre=? , description=? , image=?, date=? where id='"+t.getId()+"'"  ;
            pst = con.prepareStatement(requete);
            pst.setInt(1,t.getId_categorie());
            pst.setString(2,t.getTitre());
            pst.setString(3,t.getDescription());
            pst.setString(4,t.getImage());
            pst.setDate(5,t.getDateev());
            pst.executeUpdate();
        } catch (SQLException ex) {
            Logger.getLogger(ServiceEvent.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
    ServiceCategorie sc = new ServiceCategorie();


    @Override
    public List<Evenement> readAll() throws SQLException {
    List<Evenement> arr=new ArrayList<>();
    ste=con.createStatement();
    ResultSet rs=ste.executeQuery("select * from evenement");
     while (rs.next()) {     
                               
                Evenement p = new Evenement(rs.getInt("id"), rs.getInt("category_id"),rs.getString("titre"), rs.getString("description"),rs.getString("image"),rs.getDate("date"));

                               Category d = sc.getById(p.getId_categorie());
                p.setCattegory(d);
                p.setCategorieNom(d.getNom());
                System.out.println(p);

     arr.add(p);
     }
    return arr;
    }
    
    @Override
    public List<Evenement> getTrier() throws SQLException {
    List<Evenement> arr=new ArrayList<>();
    ste=con.createStatement();
    ResultSet rs=ste.executeQuery("select * from evenement ORDER BY titre DESC");
     while (rs.next()) {                
                Evenement p = new Evenement(rs.getInt("id"), rs.getInt("category_id"),rs.getString("titre"), rs.getString("description"),rs.getString("image"),rs.getDate("date"));

     arr.add(p);
     }
    return arr;
    }

  public Evenement getByName(String n) {
          Evenement a = null;
         String requete = " select* from evenement  where (titre like '"+n+"%')" ;
        try {
           
            ste = con.createStatement();
            res=ste.executeQuery(requete);
            if (res.next())
            {
                       a = new Evenement(res.getInt("id"), res.getInt("category_id"),res.getString("titre"), res.getString("description"),res.getString("image"),res.getDate("date"));

            }
        } catch (SQLException ex) {
            Logger.getLogger(ServiceEvent.class.getName()).log(Level.SEVERE, null, ex);
        }
        return a ;
    }


    public Evenement getById(Evenement f) {
          Evenement a = null;
         String requete = " select* from evenement  where id='"+f.getId()+"'" ;
        try {
           
            ste = con.createStatement();
            res=ste.executeQuery(requete);
            if (res.next())
            {
                               a = new Evenement(res.getInt("id"), res.getInt("category_id"),res.getString("titre"), res.getString("description"),res.getString("image"),res.getDate("date"));
            }
        } catch (SQLException ex) {
            Logger.getLogger(ServiceEvent.class.getName()).log(Level.SEVERE, null, ex);
        }
        return a ;
    }

}
