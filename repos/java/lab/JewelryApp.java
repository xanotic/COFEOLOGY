import java.util.Scanner;

public class JewelryApp {
    public static void main(String[] args) {

        Scanner scan = new Scanner(System.in);

        System.out.println("Enter the price of gold:");
        double price = scan.nextDouble();

        System.out.println("Enter the weight of gold:");
        double weight = scan.nextDouble();

        System.out.println("Enter the wage:");
        double wage = scan.nextDouble();

        Jewelry jewelObj = new Jewelry(price, weight, wage);

        System.out.println(jewelry);
    }
}
