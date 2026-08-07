package entity;

import java.util.Properties;
import javax.mail.Authenticator;
import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.PasswordAuthentication;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;

public class SendMail {
    public static void sendMail(String receveursList, String object, String corps) {
        String senderEmail = System.getenv("SMTP_USERNAME");
        String senderPassword = System.getenv("SMTP_PASSWORD");
        String smtpHost = System.getenv().getOrDefault("SMTP_HOST", "smtp.gmail.com");
        String smtpPort = System.getenv().getOrDefault("SMTP_PORT", "587");

        if (senderEmail == null || senderEmail.isBlank() || senderPassword == null || senderPassword.isBlank()) {
            System.err.println("SMTP not configured. Set SMTP_USERNAME and SMTP_PASSWORD before sending emails.");
            return;
        }

        Properties properties = new Properties();
        properties.put("mail.smtp.auth", "true");
        properties.put("mail.smtp.starttls.enable", "true");
        properties.put("mail.smtp.host", smtpHost);
        properties.put("mail.smtp.port", smtpPort);

        Session session = Session.getInstance(properties, new Authenticator() {
            @Override
            protected PasswordAuthentication getPasswordAuthentication() {
                return new PasswordAuthentication(senderEmail, senderPassword);
            }
        });

        Message message = prepareMessage(session, senderEmail, receveursList, object, corps);

        try {
            if (message != null) {
                Transport.send(message);
                System.err.println("Message envoyé avec succès");
            }
        } catch (MessagingException ex) {
            ex.printStackTrace();
        }
    }

    private static Message prepareMessage(Session session, String email, String receveursList, String object, String corps) {
        Message message = new MimeMessage(session);

        try {
            message.setFrom(new InternetAddress(email));
            message.setSubject(object);
            message.setRecipient(Message.RecipientType.TO, new InternetAddress(receveursList));
            message.setText(corps);
            return message;
        } catch (MessagingException ex) {
            ex.printStackTrace();
        }

        return null;
    }
}
