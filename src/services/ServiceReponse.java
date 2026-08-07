/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package services;

/**
 *
 * @author Skymil
 */
import entity.Reponse;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import tools.MaConnection;

public class ServiceReponse implements IService<Reponse>{
    Connection conn;
    public ServiceReponse(){
        conn=MaConnection.getInstance().getCnx();
    }

    @Override
    public void ajouter(Reponse r) {
        try {
            String query="INSERT INTO `reponse`"
                    + "( `relation_reclamation_id`, `objet`, `message`) "
                    + "VALUES ('"+r.getRelation_Reclamation_id()+"','"+r.getObjet()+"','"+r.getMessage()+"')";
            Statement st=conn.createStatement();
            st.executeUpdate(query);
        } catch (SQLException ex) {
            Logger.getLogger(ServiceReclamation.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    @Override
    public void supprimer(int id) {
        try {
            String query="DELETE FROM reponse WHERE id="+id;
            Statement st=conn.createStatement();
            st.executeUpdate(query);
        } catch (SQLException ex) {
            Logger.getLogger(ServiceReclamation.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    @Override
    public void modifier(int id, Reponse r) {
        try {
            String query="UPDATE reponse SET "
                    + "`reclamation_id`='"+r.getRelation_Reclamation_id()+"',"
                    + "`objet`='"+r.getObjet()+"',"
                    + "`message`='"+r.getMessage()+"' WHERE id="+id;
         
            Statement st=conn.createStatement();
            st.executeUpdate(query);
        } catch (SQLException ex) {
            Logger.getLogger(ServiceReclamation.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    @Override
    public List<Reponse> afficher() {
        List<Reponse> lr=new ArrayList<>();
        try {
            String query="SELECT * FROM reponse";
            Statement st=conn.createStatement();
            ResultSet rs=st.executeQuery(query);
            while(rs.next()){
                Reponse r=new Reponse();
                r.setId(rs.getInt("id"));
                r.setRelation_Reclamation_id(rs.getInt("relation_reclamation_id"));
                r.setObjet(rs.getString("objet"));
                r.setMessage(rs.getString("message"));
               
                lr.add(r);
            }
        } catch (SQLException ex) {
            Logger.getLogger(ServiceReclamation.class.getName()).log(Level.SEVERE, null, ex);
        }
        return lr;
    }

    @Override
    public Reponse getById(int id) {
        /*
        try {
            String query="SELECT * FROM reponse WHERE id="+id;
            Statement st=conn.createStatement();
            ResultSet rs=st.executeQuery(query);
            while(rs.next()){
                Reponse r=new Reponse();
                r.setId(rs.getInt("id"));
                r.setReclamation_id(rs.getInt("reclamation_id"));
                r.setObjet(rs.getString("objet"));
                r.setDescription(rs.getString("description"));
                r.setDatereponse(rs.getDate("datereponse"));
                return r;
            }
        } catch (SQLException ex) {
            Logger.getLogger(ServiceReclamation.class.getName()).log(Level.SEVERE, null, ex);
        }
        return null;*/
        return afficher().stream().filter(r->r.getId()==id).findAny().orElse(null);
    }
   

}
