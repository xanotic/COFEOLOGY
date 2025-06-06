import java.util.Scanner;
public class WorkerCustApp
{
    public static void main (String[] args)
    {
        //ans c i)
        CustomerServices custArr[] = new CustomerServices[2] ; //in the question mintak 50
        
        //ans c ii)
        Scanner scan= new Scanner(System.in);
        String cn, pn, ad, st, wn, wi, wc;
        double sa = 0, ta = 0 ;
        
        for (int i = 0; i<custArr.length ; i++)
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
            custArr[i] = new CustomerServices(); //default constructor does not match w users input bcs dont hv normal constructor. so call mutator method
            custArr[i].setCustomerServices(cn, pn, ad, st, sa, ta, w);
            
        }
        
        for (int i=0; i<custArr.length; i++)
        {
            System.out.println(custArr[i].toString());
        }
        
        //c iii)
        //displaying the id
        for(int i=0; i < custArr.length; i++) 
        {
            if (custArr[i].getServiceType().equalsIgnoreCase("Grass")); //use asseccor method
                System.out.println("Worker id who have done grass cutting job: ");
                System.out.println(custArr[i].getWorker().getWorkerId()); 
        }
        //getServiceType() retrieve value
        //called getWorker() bcs it exist in CS class to retrieve getWorkerId() (related)
    } //end main
} //end class