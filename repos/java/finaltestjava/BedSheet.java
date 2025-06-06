public class BedSheet extends HouseHoldGood
{
    private String material;
    private String size;
    private int threads;
    private String tagColour;

    //answer a
    public BedSheet(String itemID, String brand, double price, String material, String size, int threads, String tagColour)
    {
        super(itemID, brand, price);
        this.material = material;
        this.size = size;
        this.threads = threads;
        this.tagColour = tagColour;
    }
    public void setBedSheet(BedSheet bs)
    {
        this.itemID = bs.itemID;
        this.brand = bs.brand;
        this.price = bs.price;
        this.material = bs.material;
        this.size = bs.size;
        this.threads = bs.threads;
        this.tagColour = bs.tagColour;
    }

    public String getMaterial()
    {
        return material;
    }

    public String getSize()
    {
        return size;
    }

    public int getThreads()
    {
        return threads;
    }

    public String getTagColour()
    {
        return tagColour;
    }
    //answer b
    public String toString()
    {
        return "Material: " + material + "\nSize: " + size + "\nthreads: " + threads;
    }

    //answer c
    public double calcAfterDisc(String tagColour)
    {
        double priceafterdisc = 0;

        if (tagColour.equalsIgnoreCase("Yellow"))
        {
            priceafterdisc = price * 0.7;
        }
        else if (tagColour.equalsIgnoreCase("Blue"))
        {
            priceafterdisc = price * 0.75;
        }
        else
        {
            priceafterdisc = price;
        }

        return priceafterdisc;
    }
}