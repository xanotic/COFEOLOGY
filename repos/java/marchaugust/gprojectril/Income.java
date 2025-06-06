package gprojectril;

public class Income
{
    private String incDate;
    private String incCategory;
    private double incAmount;
    public Income(String incDate, String incCategory, double incAmount)
    {
        this.incDate = incDate;
        this.incCategory = incCategory;
        this.incAmount = incAmount;
    }

    public String getIncDate()
    {
        return incDate;
    }

    public String getIncCategory()
    {
        return incCategory;
    }

    public double getIncAmount()
    {
        return incAmount;
    }

}