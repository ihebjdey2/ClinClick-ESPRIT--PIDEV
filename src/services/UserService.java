/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package services;

import entity.User;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Properties;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.PasswordAuthentication;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;
import static jdk.nashorn.internal.runtime.Debug.id;
import tools.MaConnection;


/**
 *
 * @author Ayoub
 */
public class UserService {
    public static PreparedStatement ps;
    Connection cnx;
    public static int code;
    public UserService() {
        cnx = MaConnection.getInstance().getCnx();    
    }
    
    
    public void createuser(User u){
        try {
            String req = "INSERT INTO `user`(`email`, `roles`, `password`, `is_verified`, `date_naissance`, `nom`, `prenom`, `genre`) VALUES (?,?,?,?,?,?,?,?)";
            PreparedStatement st = cnx.prepareStatement(req);
            st.setString(1, u.getEmail());
            st.setString(2, u.getRoles());
            st.setString(3, u.getPassword());
            st.setBoolean(4, false);
            st.setDate(5, (Date) u.getDate_naissance());
            st.setString(6,u.getNom());
            st.setString(7, u.getPrenom());
            st.setString(8, u.getGenre());
            
            st.executeUpdate();
            System.out.println(" User Added successfuly");
                 
        } catch (SQLException ex) {
            System.err.println(ex.getMessage());
        }
    }
    public ArrayList<User> getAllUsers() {
        ArrayList<User> userList = new ArrayList<>();
        try {
            String req = "SELECT * FROM `user`";
            Statement st = cnx.createStatement();
            ResultSet rs = st.executeQuery(req);
            while(rs.next()){
                User u = new User();
                u.setId(rs.getInt("id"));
                u.setEmail(rs.getString("email"));
                u.setRoles(rs.getString("roles"));
                u.setPassword(rs.getString("password"));
                u.setIs_verified(rs.getBoolean("is_verified"));
                u.setDate_naissance(rs.getDate("date_naissance")); 
                u.setNom(rs.getString("nom"));
                u.setPrenom(rs.getString("prenom"));
                u.setGenre(rs.getString("genre"));
                userList.add(u);
            }
        } catch (SQLException ex) {
            System.err.println(ex.getMessage());
        }
        return userList;
    }
    public User getUserById(int id) {
        User u = new User();
        try {
            String req = "SELECT * FROM `user` WHERE id=?";
            PreparedStatement st = cnx.prepareStatement(req);
            st.setInt(1, id);
            ResultSet rs = st.executeQuery();
            while(rs.next()){
                u.setId(rs.getInt("id"));
                u.setEmail(rs.getString("email"));
                u.setRoles(rs.getString("roles"));
                u.setPassword(rs.getString("password"));
                u.setIs_verified(rs.getBoolean("is_verified"));
                u.setDate_naissance(rs.getDate("date_naissance"));
                u.setNom(rs.getString("nom"));
                u.setPrenom(rs.getString("prenom"));
                u.setGenre(rs.getString("genre"));
            }
        } catch (SQLException ex) {
            System.err.println(ex.getMessage());
        }
        return u;
    }
    public void updateUser(User u) {
        try {
            String req = "UPDATE `user` SET `email`=?,`roles`=?,`password`=?,`is_verified`=?,`date_naissance`=?,`nom`=?,`prenom`=?,`genre`=? WHERE id=?";
            PreparedStatement st = cnx.prepareStatement(req);
            st.setString(1, u.getEmail());
            st.setString(2, u.getRoles());
            st.setString(3, u.getPassword());
            st.setBoolean(4, u. getIs_verified());
            st.setDate(5, (Date) u.getDate_naissance());
            st.setString(6,u.getNom());
            st.setString(7, u.getPrenom());
            st.setString(8, u.getGenre());
            st.setInt(9, u.getId());
            
            st.executeUpdate();
            System.out.println(" User Updated successfuly");
                 
        } catch (SQLException ex) {
            System.err.println(ex.getMessage());
        }
    }
    public void deleteUser(User u) {
        try {
            String req = "DELETE FROM `user` WHERE id=?";
            PreparedStatement st = cnx.prepareStatement(req);
            st.setInt(1, u.getId());
            st.executeUpdate();
            System.out.println(" User Deleted successfuly");
                 
        } catch (SQLException ex) {
            System.err.println(ex.getMessage());
        }
    }

public User getUserByMail(String mail) throws SQLException {
        User u=null;

        try{
        String req="select * from user where email='"+mail+"'";
        ps=cnx.prepareStatement(req);
        ResultSet rst=ps.executeQuery();
        if(rst.first()){
            u=new User(rst.getInt(1),rst.getString(2),rst.getString(3),rst.getString(4),
                rst.getBoolean(5),rst.getDate(6),rst.getString(7),rst.getString(8),rst.getString(9));
              }
        
        }catch (SQLException ex){
            System.out.println(ex.getMessage());
            return u;
        }
        return u;
    }
/***********************************************************************************************/
    public void sendEmail(String email,String login) throws IOException {
        //  la session pour l'envoie d'un email
        Properties props = new Properties();
        props.put("mail.smtp.auth", "true");
        props.put("mail.smtp.starttls.enable", "true");
        props.put("mail.smtp.host", "smtp.gmail.com");
        props.put("mail.smtp.port", "587");
        Session session = Session.getInstance(props,
                new javax.mail.Authenticator() {
            protected PasswordAuthentication getPasswordAuthentication() {
                return new PasswordAuthentication("clinicesprit2023@gmail.com", "jzwoflvznqkdimjn");
            }
        });
        try {
            // Création de l'objet Message
            Message message = new MimeMessage(session);
            // from 
            message.setFrom(new InternetAddress("clinicesprit2023@gmail.com"));
            // Recipients
            message.setRecipients(Message.RecipientType.TO, InternetAddress.parse(email));
            // Objet de l'email
            message.setSubject("Welcome to Clinic! Let's get you set up");
        //////////////////////////////////////////////////////    
            message.setSentDate(new Date(System.currentTimeMillis()));
            String htmlBody = new String(Files.readAllBytes(Paths.get("src/mail.html")));
            String htmlBody2 = new String(htmlBody).replace("XXYY",login);
          
            message.setContent(htmlBody2, "text/html");
                     
            //  Envoyer le message
            Transport.send(message);
        } catch (MessagingException e) {
            System.out.println(e.getMessage());
        }

    }  

    public ObservableList<User> search3(String searchTerm) {
    ObservableList<User> list = FXCollections.observableArrayList();
    try {
        String query = "SELECT * FROM user WHERE nom LIKE ?";
        PreparedStatement preparedStatement = cnx.prepareStatement(query);
        preparedStatement.setString(1, searchTerm + "%");
        ResultSet rs = preparedStatement.executeQuery();
        while (rs.next()) {
            User user = new User(
                    rs.getString(1), //assuming the id is the first column
                    rs.getString(2), 
                    rs.getString(3), 
                    rs.getString(4),
                    rs.getString(5)
            );
            list.add(user);
        }
    } catch (SQLException e) {
        e.printStackTrace();
    }
    return list;
}
}


    

