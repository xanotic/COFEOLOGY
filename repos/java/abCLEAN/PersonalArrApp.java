import java.util.Scanner;

public class PersonalArrApp
{
    public static void main(String[] args)
    {
        Personal[] pArr = new Personal[3];
        String cn, pn, ad, st, wn, wi, wc;
        double sa = 0, ta = 0, disc ;
        Scanner scan= new Scanner(System.in);
        
        for (int i = 0 ; i<pArr.length; i++)
        {
            System.out.println("Enter customer name: ") ;
            cn = scan.next() ;
            System.out.println("Enter customer phone number: ");
            pn = scan.next();
            System.out.println("Enter customer address: ");
            ad = scan.next() ;
            System.out.println("Service type: ") ;
            st = scan.next();
            if (st.equalsIgnoreCase("Grass"))
            {
                System.out.println("Enter service area: ");
                sa = scan.nextDouble();
            }
            else if (st.equalsIgnoreCase("Room"))
            {
                System.out.println("Enter service area: ");
                sa = scan.nextDouble();  
                System.out.println("Enter toilet area: ");
                ta = scan.nextDouble();
            }
            
            System.out.println("Enter worker name: ") ;
            wn = scan.next() ;
            System.out.println("Enter worker ID: ") ;
            wi = scan.next() ;
            System.out.println("Enter worker contact: ") ;
            wc = scan.next() ;

            
            Worker w = new Worker(wn, wi, wc); //aka Worker wrk
            pArr[i] = new Personal(); //default constructor does not match w users input bcs dont hv normal constructor. so call mutator method
            pArr[i].setCustomerServices(cn, pn, ad, st, sa, ta, w);
        }
        for (int x = 0; x<pArr.length; x++)
        {
            System.out.println("Customer "+(x+1)+pArr);
        }
    }
}