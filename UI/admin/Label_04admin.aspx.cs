using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data.SqlClient;
using System.Data;
using System.Configuration;

public partial class UI_admin_Label_04 : System.Web.UI.Page
{
    public static String MemID = String.Empty;
    public static String MemName = String.Empty;
    public static String MemRole = String.Empty;


    string sponserid = string.Empty;
    string cs = ConfigurationManager.ConnectionStrings["harihomescn"].ConnectionString;
    int id = 0;
    decimal m = 0;
    protected void Page_Load(object sender, EventArgs e)
    {
        MemID = Convert.ToString(Session["MemberID"]);
        sponserid = Convert.ToString(Session["SponsorID"]);
        id = Convert.ToInt32(Session["id"]);
        if (!IsPostBack)
        {
            BindDetail();
        }
    }
    public void BindDetail()
    {
        SqlConnection con = new SqlConnection(cs);


        SqlCommand cmd = new SqlCommand("select ID,MemberID,SponsorID,MemberName,MobileNo ,JoiningDate,4 as Amount  from Member_Details    where SponsorID in (select MemberID from Member_Details where SponsorID in(select MemberID from Member_Details where SponsorID in(select MemberID from Member_Details  where SponsorID='" + MemID + "' and  Id>" + id + " and  Status='Active' ))) and Status='Active' ", con);

        // SqlCommand cmd = new SqlCommand("select *  from frmnewmember  where sponsorid ='" + sponserids + "' and  Id>" + id + " and status='Active' ", con);
        SqlDataAdapter da = new SqlDataAdapter(cmd);
        DataTable dt = new DataTable();
        da.Fill(dt);
        if (dt.Rows.Count > 0)
        {
            GridView1.DataSource = dt;
            GridView1.DataBind();
        }
    }
    protected void GridView1_PageIndexChanging(object sender, GridViewPageEventArgs e)
    {
        GridView1.PageIndex = e.NewPageIndex;
        BindDetail();

    }
    protected void GridView1_RowDataBound(object sender, GridViewRowEventArgs e)
    {

        if (e.Row.RowType == DataControlRowType.DataRow)
        {

            Label Salary = (Label)e.Row.FindControl("lbAmount");

            //Label lblUnitsInStock = (Label)e.Row.FindControl("lblUnitsInStock");
            m = m + decimal.Parse(Salary.Text);
            //Label lblTotalPrice = (Label)e.Row.FindControl("Salary");
            //lblTotalPrice.Text = m.ToString();
            //Table tb = new Table();
            txttotal.Text = m.ToString();
            // Session["Level1"]= m.ToString();
        }
        if (e.Row.RowType == DataControlRowType.Footer)
        {
            Label lblTotalPrice = (Label)e.Row.FindControl("Salary");
            lblTotalPrice.Text = m.ToString();
        }
    }
}