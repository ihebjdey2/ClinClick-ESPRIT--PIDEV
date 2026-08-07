package Utils;

import com.twilio.Twilio;
import com.twilio.rest.api.v2010.account.Message;
import com.twilio.type.PhoneNumber;
import Entites.Article;

public class sms {
    // Configure these values outside the repository.
    // Twilio push protection flags hard-coded SID/token pairs, so keep them in env vars.
    public static final String ACCOUNT_SID = System.getenv("TWILIO_ACCOUNT_SID");
    public static final String AUTH_TOKEN = System.getenv("TWILIO_AUTH_TOKEN");

    public static void sendsms(int x) {
        if (ACCOUNT_SID == null || ACCOUNT_SID.isBlank() || AUTH_TOKEN == null || AUTH_TOKEN.isBlank()) {
            System.err.println("Twilio not configured. Set TWILIO_ACCOUNT_SID and TWILIO_AUTH_TOKEN.");
            return;
        }

        Twilio.init(ACCOUNT_SID, AUTH_TOKEN);
        Message message = Message.creator(
                new PhoneNumber("+21624485249"),
                new PhoneNumber("+13203772079"),
                "le date d'expiration du stock d'identifiant " + x + " a ete supprimer!!!")
            .create();

        System.out.println(message.getSid());
    }
}
