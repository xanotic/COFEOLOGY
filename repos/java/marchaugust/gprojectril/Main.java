package gprojectril;

import java.io.*;
import java.util.*;

public class Main
{
    public static void main(String[] args) throws IOException
    {
        ArrayList<Expense> expList = new ArrayList<Expense>();
        ArrayList<Income> incList = new ArrayList<Income>();
        String type;
        String date;
        String category;
        double amount;
        
        double totalExp = 0;
        double totalInc = 0;

        try
        {
            FileReader fr = new FileReader("C:\\Users\\User\\repos\\java\\marchaugust\\gprojectril\\record.txt");
            BufferedReader br = new BufferedReader(fr);

            String dataExp = null;
            StringTokenizer st = null;

            while ((dataExp = br.readLine()) != null)
            {
                st = new StringTokenizer(dataExp, ";");
                type = st.nextToken();
                date = st.nextToken();
                category = st.nextToken();
                amount = Double.parseDouble(st.nextToken());

                if ( type.equalsIgnoreCase("expense"))
                {
                    Expense expMenu = new Expense(date, category, amount);
                    expList.add(expMenu);
                }
                else if (type.equalsIgnoreCase("income"))
                {
                    Income incMenu = new Income(date, category, amount);
                    incList.add(incMenu);
                }
            }
            br.close();

            System.out.println("Expense");
            if (expList.size() == 0)
            {
                System.out.println("Expense List is empty.\n");
            }
            else
            {
                System.out.printf("%-20s%-20s%-20s\n", "Date", "Category", "Amount");
                System.out.println("---------------------------------------------------------------");

                for (int i = 0; i < expList.size(); i++)
                {
                    System.out.printf("%-20s%-20s%-20s\n", expList.get(i).getExpDate(), expList.get(i).getExpCategory(), expList.get(i).getExpAmount());
                    System.out.println("---------------------------------------------------------------");
                    totalExp = totalExp + expList.get(i).getExpAmount();
                }
                System.out.printf("%-20s%-20s%-20s\n", " ", "Total Expense:", totalExp);
            }
            totalExp = 0;
            
            System.out.println("\n\n");

            System.out.println("Income");
            if (incList.size() == 0)
            {
                System.out.println("Income List is empty.\n");
            }
            else
            {
                System.out.printf("%-20s%-20s%-20s\n", "Date", "Category", "Amount");
                System.out.println("---------------------------------------------------------------");

                for (int j = 0; j < incList.size(); j++)
                {
                    System.out.printf("%-20s%-20s%-20s\n", incList.get(j).getIncDate(), incList.get(j).getIncCategory(), incList.get(j).getIncAmount());
                    System.out.println("---------------------------------------------------------------");
                    totalInc = totalInc + incList.get(j).getIncAmount();
                }
                System.out.printf("%-20s%-20s%-20s\n", " ", "Total Income:", totalInc);
            }
            totalInc = 0;

            System.out.println("\n\n");
        }

        catch(FileNotFoundException fnf)
        {
            System.out.println(fnf.getMessage());
        }

        catch(EOFException eof)
        {
            System.out.println(eof.getMessage());
        }

        catch(IOException io)
        {
            System.out.println(io.getMessage());
        }

        finally
        {
            System.out.println("System end here..TQ!!");
        }
    
    }
}