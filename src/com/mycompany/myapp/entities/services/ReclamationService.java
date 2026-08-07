/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.myapp.entities.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.CharArrayReader;
import com.codename1.io.ConnectionRequest;
import com.codename1.io.JSONParser;
import com.codename1.io.Log;
import com.codename1.io.NetworkEvent;
import com.codename1.io.NetworkManager;
import com.codename1.ui.Font;
import com.codename1.ui.events.ActionListener;
import com.itextpdf.text.BaseColor;
import com.mycompany.myapp.entities.Reclamation;
import com.mycompany.myapp.utils.Statics;
import com.sun.javafx.font.FontFactory;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import org.json.JSONException;
import org.json.JSONObject;
import com.itextpdf.text.Document;

import com.itextpdf.text.Paragraph;
import com.itextpdf.text.Phrase;
import com.itextpdf.text.pdf.PdfPCell;
import com.itextpdf.text.pdf.PdfPTable;
import com.itextpdf.text.pdf.PdfWriter;
import java.io.FileOutputStream;

/**
 *
 * @author tlich
 */
public class ReclamationService {

    public static ReclamationService instance = null;
    public int resultCode;
    public static boolean resultOk = true;
    private ArrayList<Reclamation> listReclamations;

    //initilisation connection request 
    private ConnectionRequest req;
    private int et;

    public static ReclamationService getInstance() {
        if (instance == null) {
            instance = new ReclamationService();
        }
        return instance;
    }

    public ReclamationService() {
        req = new ConnectionRequest();

    }

   public int ajoutReclamation(Reclamation reclamation) {
    String url = Statics.BASE_URL + "/mobile/reclamation/add_reclamation";
    ConnectionRequest request = new ConnectionRequest();
    request.setUrl(url);
    request.setPost(true);
    request.addArgument("nom", reclamation.getNom());
    request.addArgument("email", reclamation.getEmail());
    request.addArgument("description", reclamation.getDescription());
    request.addArgument("etat", "0");
    request.addResponseListener((e) -> {
        String str = new String(request.getResponseData());
        System.out.println("data == "+str);
    });
    NetworkManager.getInstance().addToQueueAndWait(request);
    request.addResponseListener(new ActionListener<NetworkEvent>() {
        @Override
        public void actionPerformed(NetworkEvent evt) {
            resultCode = request.getResponseCode();
            request.removeResponseListener(this);
        }
    });
    try {
        request.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
        NetworkManager.getInstance().addToQueueAndWait(request);
    } catch (Exception ignored) {

    }
    return resultCode;
}


    //Delete 
    public boolean deleteReclamation(int id) {
        String url = Statics.BASE_URL + "/mobile/reclamation/delete/" + id;

        req.setUrl(url);

        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                req.removeResponseCodeListener(this);
            }
        });

        NetworkManager.getInstance().addToQueueAndWait(req);
        return resultOk;
    }

    //Update 
    public int modifierReclamation(Reclamation Reclamation) {
        String url = Statics.BASE_URL + "/mobile/reclamation/edit_reclamation/"+ Integer.toString(Reclamation.getId());
        req.setUrl(url);
         req.setPost(true);
    req.addArgument("nom", Reclamation.getNom());
    req.addArgument("email", Reclamation.getEmail());
    req.addArgument("Description", Reclamation.getDescription());

        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                resultOk = req.getResponseCode() == 200 ;  // Code response Http 200 ok
                req.removeResponseListener(this);
            }
        });
     NetworkManager.getInstance().addToQueueAndWait(req);//execution ta3 request sinon yet3ada chy dima nal9awha
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                resultCode = req.getResponseCode();
                req.removeResponseListener(this);
            }
        });
        try {
            req.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(req);
        } catch (Exception ignored) {

        }
        return resultCode;

    }

    public ArrayList<Reclamation> getAll() {
          listReclamations = new ArrayList<Reclamation>();

        req = new ConnectionRequest();
        req.setUrl(Statics.BASE_URL + "/mobile/reclamation/afficher");
        req.setHttpMethod("GET");


        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
if (req.getResponseCode() == 200) {
                    
                        listReclamations = getList();
                }

                req.removeResponseListener(this);
            
            }
        });

        try {
//            req.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(req);
        } catch (Exception e) {
            e.printStackTrace();
        }
        System.out.println(listReclamations);
        return listReclamations;
    }
    
    
    public void pdf(){
        Document document = new Document();
        try {
            PdfWriter.getInstance(document, new FileOutputStream("Reclamation.pdf"));
            document.open();
            
            // Create a Table object with 4 columns
            PdfPTable table = new PdfPTable(new float[]{2, 2, 2, 2});

            // Add table headers
            table.addCell(new PdfPCell(new Paragraph("id")));
            table.addCell(new PdfPCell(new Paragraph("nom")));
            table.addCell(new PdfPCell(new Paragraph("email")));
            table.addCell(new PdfPCell(new Paragraph("description")));


            // Loop through the ArrayList and add data to the table
            for (Reclamation reclamation : listReclamations) {
                table.addCell(new PdfPCell(new Paragraph(String.valueOf(reclamation.getId()))));
                table.addCell(new PdfPCell(new Paragraph(reclamation.getNom())));
                table.addCell(new PdfPCell(new Paragraph(reclamation.getEmail())));
                table.addCell(new PdfPCell(new Paragraph(reclamation.getDescription())));
              
           
            }

            // Add the table to the Document
            document.add(table);
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            document.close();
        }
    }

    private ArrayList<Reclamation> getList() {
        try {
       Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(req.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                if (obj.get("etat").toString() == "false") {
                    et = 0;
                } else {
                    et = 1;
                }
                Reclamation Reclamation = new Reclamation(
                        (int) Float.parseFloat(obj.get("id").toString()),
                        obj.get("nom").toString(),
                        obj.get("email").toString(),
                        obj.get("description").toString(),
                        et
                );

                listReclamations.add(Reclamation);
            }
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        return listReclamations;
    }
public Reclamation findRec(int id) {
    String url = Statics.BASE_URL+"/mobile/reclamation/findrec/"+id;
    req.setUrl(url);
    req.setPost(false);
      
    NetworkManager.getInstance().addToQueueAndWait(req);
   
     try {
    String response = new String(req.getResponseData(), "UTF-8");
    System.out.println(response);
    System.out.println("Response string: " + response);
    JSONObject jsonObj = new JSONObject(response);
    Reclamation reclamation = parseReclamation(jsonObj);
    return reclamation;
} catch (JSONException | UnsupportedEncodingException ex) {
    Log.e(ex);
    return null;
}
}



private Reclamation parseReclamation(JSONObject jsonObj) {
    try {
        Reclamation reclamation = new Reclamation();
        reclamation.setId(jsonObj.getInt("id"));
        reclamation.setNom(jsonObj.getString("nom"));
      
        reclamation.setDescription(jsonObj.getString("description"));
        
        return reclamation;
    } catch (JSONException ex) {
        Log.e(ex);
        return null;
    }
}}
