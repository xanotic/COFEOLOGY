import java.util.Scanner; //import macam define dalam c++

public class carapp 
{
    public static void main(String args[])//header
    { 
        String brand, model;
        int year;
        car c; //declare object. before pakai object kena declare dulu baru bole pakai
        c = new car(); //create object. new ni untuk allocate memory dalam object
        car c2 = new car();  //create and define object at the same time
        car c3 = new car();
        Scanner scan = new Scanner (System.in); //create scanner object for user input. System.in represent keyboard

        c.define();
        c.display();
        c2.selfDefine("Porsche", "911 Turbo S", 2020);
        c2.display(); 

        System.out.println("Enter Brand name");
        brand = scan.next(); //read brand name from user input
        System.out.println("Enter Model name");
        model = scan.next();
        System.out.println("Enter Year made");
        year = scan.nextInt();
        c3.selfDefine(brand, model, year);
        c3.display();
    }
}