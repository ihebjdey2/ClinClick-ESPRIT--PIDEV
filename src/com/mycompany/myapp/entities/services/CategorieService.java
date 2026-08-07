/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.myapp.entities.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.CharArrayReader;
import com.codename1.io.ConnectionRequest;
import com.codename1.io.JSONParser;
import com.codename1.io.NetworkEvent;
import com.codename1.io.NetworkManager;
import com.codename1.ui.events.ActionListener;
import com.mycompany.myapp.entities.Categorie;
import com.mycompany.myapp.utils.Statics;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

/**
 *
 * @author mohta
 */
public class CategorieService {
    
    public static CategorieService instance = null ;
    public int resultCode;
    public static boolean resultOk = true;
    private ArrayList<Categorie> listCategories;
    //initilisation connection request 
    private ConnectionRequest req;
    
    
    public static CategorieService getInstance() {
        if(instance == null )
            instance = new CategorieService();
        return instance ;
    }
    
    
    
    public CategorieService() {
        req = new ConnectionRequest();
        
    }
    
  public ArrayList<Categorie> getcat(int id) {
        listCategories = new ArrayList<Categorie>();

        req = new ConnectionRequest();
        req.setUrl(Statics.BASE_URL + "/mobile/Categorie/"+id);
        req.setHttpMethod("GET");

        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (req.getResponseCode() == 200) {
                    listCategories = getList();
                }

                req.removeResponseListener(this);
            }
        });

        try {
            req.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(req);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return listCategories;
    }
  
  
  public ArrayList<Categorie> getAll() {
        listCategories = new ArrayList<Categorie>();

        req = new ConnectionRequest();
        req.setUrl(Statics.BASE_URL + "/mobile/Categorie");
        req.setHttpMethod("GET");

        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (req.getResponseCode() == 200) {
                    listCategories = getList();
                }

                req.removeResponseListener(this);
            }
        });

        try {
            req.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(req);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return listCategories;
    }

    private ArrayList<Categorie> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(req.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                Categorie Categorie = new Categorie(
                       (int) Float.parseFloat(obj.get("id").toString()),
                        obj.get("nom").toString(),
                        obj.get("information").toString()
                );

                listCategories.add(Categorie);
            }
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        return listCategories;
    }
}
