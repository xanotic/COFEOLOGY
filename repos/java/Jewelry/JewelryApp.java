import java.util.Scanner;
/**
 * Write a description of class JewelryApp here. *
 * @author (your name) * @version (a version number or a date)
 */public class JewelryApp
{    public static void main (String[] args)
    {        //question b)i.
        double goldPrice, goldWeight, goldWage;        
        Scanner scan = new Scanner(System.in); 
        
        System.out.println("Enter gold price   :  ");
        goldPrice = scan.nextDouble();        
        System.out.println("Enter gold weight  :  ");
        goldWeight = scan.nextDouble();       
        System.out.println("Enter gold wage    :  ");
        goldWage = scan.nextDouble();  
        
        //contructor        
        Jewelry jewelObj = new Jewelry(goldPrice, goldWeight, goldWage);
        
        //question b)ii.
        jewelObj.setGoldPrice(295.01);
        
        //question b)iii.
        double newPrice=0;
        if (jewelObj.getGoldWeight() >= 40)
        {            newPrice = jewelObj.calcTotPrice() * 0.95;
        }        else if (jewelObj.getGoldWeight() >= 50)
        {            newPrice = jewelObj.calcTotPrice() * 0.90;
        }        else if (jewelObj.getGoldWeight() >= 100)
        {            newPrice = jewelObj.calcTotPrice() * 0.85;
        }        else if (jewelObj.getGoldWeight() >= 200)
        {            newPrice = jewelObj.calcTotPrice() * 0.80;
        }        
        //question b)iv.        
        System.out.println ("*");
        System.out.println ("Detail Price:");        
        System.out.println ("Gold Price  : RM " + jewelObj.getGoldPrice());
        System.out.println ("Gold Weight : RM " + jewelObj.getGoldWeight() +" gram");        
        System.out.println ("Gold Wage   : RM " + jewelObj.getGoldWage());
        System.out.println ("Discount    : RM " + (jewelObj.calcTotPrice()-newPrice));
        
        Jewelry jewelObj2 = new Jewelry (230.30, 15.3, 210.00);
        Jewelry a = jewelObj.moreExpensive (jewelObj2);
        
        System.out.println("The more expensive is " + a.toString());
        
        
        
    }//end method main
    } //end class}