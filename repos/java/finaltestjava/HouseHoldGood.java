public class HouseHoldGood
{
    protected String itemID;
    protected String brand;
    protected double price;

    public HouseHoldGood(String itemID, String brand, double price)
    {
        this.itemID = itemID;
        this.brand = brand;
        this.price = price;
    }

    public void setHouseHoldGood(HouseHoldGood hhg)
    {
        this.itemID = hhg.itemID;
        this.brand = hhg.brand;
        this.price = hhg.price;
    }

    public String getItemID()
    {
        return itemID;
    }

    public String getBrand()
    {
        return brand;
    }
    public double getPrice()
    {
        return price;
    }   
}