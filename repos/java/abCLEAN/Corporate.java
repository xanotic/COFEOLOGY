public class Corporate extends CustomerServices
{
    private char packages;
    /* package A = RM10000
     * package B = RM20000
     * package C = RM30000 
     */
    
    public Corporate()
    {
        super();
        packages = 'x';
    }
    
    public void setCooporate(String cn, String pn, String ad, String st, double sa, double ta, Worker w, char x)
    {
        setCustomerServices(cn, pn, ad, st, sa, ta, w);
        packages = x;
    }
    
    public char getPackages()
    {
        return packages;
    }
    
    public double calcServiceCharge()
    {
        double price = 0;
        
        if (packages == 'A')
        {
            price = 10000;
        }
        
        else if (packages == 'B')
        {
            price = 20000;
        }
        
        else
        {
            price = 30000;
        }
        
        return price;
    }
    
    public String toString()
    {
        return super.toString() + packages;
    }
}