package q4;
import java.util.Scanner;

public class Main
{
    public static void main(String[] args)
    {
        String custName;
        String phoneNo;
        String location;
        char serviceType;
        int duration;
        String staffName;
        String staffId;
        String staffContact;

        //answer ci
        
        HomeCare[] assist = new HomeCare[30];

        //answer cii

        Scanner scan = new Scanner(System.in);
        for (int i = 0; i < assist.length; i++)
        {
            System.out.print("cust name: ");
            custName = scan.nextLine();
            System.out.print("phone no: ");
            phoneNo = scan.nextLine();
            System.out.print("location: ");
            location = scan.nextLine();
            System.out.print("service type: ");
            serviceType = scan.next().charAt(0);
            System.out.print("duration: ");
            duration = scan.nextInt();
            System.out.print("staff name: ");
            staffName = scan.nextLine();
            System.out.print("staff id: ");
            staffId = scan.nextLine();
            System.out.print("staff contact: ");
            staffContact = scan.nextLine();
            scan.nextLine();

            Staff stf = new Staff(staffName, staffId, staffContact);
            assist[i] = new HomeCare(custName, phoneNo, location, serviceType, duration, stf);
        }

        //answer ciii
        double totalCharge = 0;
        for (int j = 0; j < assist.length; j++)
        {
            if (assist[j].getServiceType() == 'T')
            {
                totalCharge = assist[j].calcServiceCharge();
            }
        }
        System.out.println(totalCharge);
    }

    double totalCharge = 0;
    for (int m = 0; m < assist.length; m++){
        if (assist[m].getServiceType()== 'T'){
            totalCharge = assist[m].calcServiceCharge();
        }
    }
    system.out.println(totalCharge);
}
