import java.util.Scanner;
public class AgeApp
{
    public static void main (String[] args)
    {
        String icNum;
        Scanner scan = new Scanner(System.in);
        System.out.println("Enter your ic number:");
        icNum = scan.nextLine();
        
        Age2 ageObj = new Age2();
        int age = ageObj.calcAge(icNum);
        
        System.out.println("Your age is : "+age+"years");
    }
}