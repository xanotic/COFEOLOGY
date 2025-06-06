import java.util.Scanner;
public class Main
{
    public static void main(String[] args)
    {
        Scanner inputText = new Scanner(System.in);
        BedSheet[] bedS = new BedSheet[1];
        

        for (int i = 0; i < bedS.length; i++)
        {
            System.out.println("\nEnter item id:");
            String itemID = inputText.nextLine();
            System.out.println("\nEnter item brand:");
            String brand = inputText.nextLine();
            System.out.println("\nEnter price:");
            double price = inputText.nextDouble();
            inputText.nextLine();

            System.out.println("\nEnter material:");
            String material = inputText.nextLine();
            System.out.println("\nEnter size:");
            String size = inputText.nextLine();
            System.out.println("\nEnter count threads:");
            int threads = inputText.nextInt();
            inputText.nextLine();

            System.out.print("\nEnter color: ");
            String color = inputText.nextLine();
        
         //answer d1
            bedS[i] = new BedSheet(itemID, brand, price, material, size, threads);
        }

        //answer d2
        for (int j = 0; j < bedS.length; j++)
        {
            if (bedS[j].getSize().equalsIgnoreCase("King"))
            {
                System.out.println(bedS[j].getItemID());
            }
        }

        //answer d3
        for (int k = 0; k < bedS.length; k++)
        {
            System.out.println(bedS[k].calcAfterDisc(bedS[k].getTagColour()));
        }

        // answer d4
        for (int l = 0; l < bedS.length; l++)
        {
            System.out.println(bedS[l].getPrice());
        }


        inputText.close();
    }
}
