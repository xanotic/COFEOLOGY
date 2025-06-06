public class Jewelry{
    private double price;
    private double weight;
    private double wage;

    //normal constructor
    public Jewelry(double x,double y,double z){
        price = x;
        weight = y;
        wage = z;
    }
    //mutator
    public void setGoldPrice(double x){
        price = x;
    }

    public void setGoldWeight(double y){
        weight = y;
    }

    public void setGoldWage(double z){
        wage = z;
    }
    //accesspr
    public double getGoldPrice(){
        return price;
    }
    public double getGoldWeight(){
        return weight;
    }
    public double getGoldWage(){
        return wage;
    }   
    //processor
    public double calcTotPrice(){
        double a;
        a = (price*weight)+wage;
        return a;
    }
    //printer method
    public String toString(){
        return "Jewelry Details:\n" + "Price of gold: $" + price + "\n" + "Weight of gold: " + weight + " grams\n" + "Wage: $" + wage + "\n" + "Total price: $" + calcTotPrice();
    }

    

}