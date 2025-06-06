
public class Jewelry
{
    // instance variables - replace the example below with your own
    private double GoldPrice;
    private double GoldWeight;
    private double GoldWage;
    
    public Jewelry(double p, double w, double g)
    {
        GoldPrice = p;
        GoldWeight = w;
        GoldWage = g;
    }
    
    public Jewelry()
    {
        GoldPrice = 400 ;
        GoldWeight = 3 ;
        GoldWage = 300;
    }

    public void setJewelry(double GoldPrice, double goldWeight){
        this.GoldPrice = GoldPrice;
        this.GoldWeight = GoldWeight;
    }

    public void setJewelry(double GoldPrice, double goldWeight, double goldWage){
        this.GoldPrice = GoldPrice;
        this.GoldWeight = GoldWeight;
        this.GoldWage = GoldWage;
    }



    //mutator
    public void setGoldPrice(double p)
    {
        GoldPrice=p;
    }
    public void setGoldWeight(double w)
    {
        GoldWeight=w;
    }
    public void setGoldWage(double g)
    {
        GoldWage=g;
    }
    //accessor
    public double getGoldPrice()
    {
        return GoldPrice;
    }
    public double getGoldWeight()
    {
        return GoldWeight;
    }
    public double getGoldWage()
    {
        return GoldWage;
    }
    //proccessor
    public double calcTotPrice()
    {
        double Totprice;
        Totprice = (GoldPrice * GoldWeight) + GoldWage;
        return Totprice;
    }
    public Jewelry moreExpensive (Jewelry j)
    {
        if(this.calcTotPrice() > j.calcTotPrice())
            return this;
        else 
            return j;
    }
    
    //printer 
    public String toString()
    {
        return "Gold Price = RM " +GoldPrice +"Gold Weight =" +GoldWeight +"Gold Wage =" +GoldWage;
    }
    
    }